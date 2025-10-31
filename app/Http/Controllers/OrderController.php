<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $orders = Order::query();
        if ($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $orders->where(function ($query) use ($search) {
                $query->whereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where(function ($nameQuery) use ($search) {
                        $nameQuery->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $search . '%')
                            ->orWhere('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
                });

                if (is_numeric($search)) {
                    $query->orWhere('id', (int) $search);
                }
            });
        }
        $orders = $orders->with(['items.product', 'payments', 'customer', 'store', 'items.product.productDetail'])
            ->where('store_id', $user->store_id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $total = $orders->map(function ($i) {
            return $i->total();
        })->sum();
        $receivedAmount = $orders->map(function ($i) {
            return $i->receivedAmount();
        })->sum();

        // return response()->json($orders);

        return view('orders.index', compact('orders', 'total', 'receivedAmount'));
    }

    public function store(OrderStoreRequest $request)
    {
        $user = auth()->user();
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'user_id' => $request->user()->id,
            'store_id' => $user->store_id,
        ]);

        // Calculate total amount and received amount
        $totalAmount = floatval($request->amount);
        $receivedAmount = floatval($request->received_amount ?? $request->amount);
        
        // Calculate total items value for proportional distribution
        $totalItemsValue = 0;
        foreach ($request->items as $itemData) {
            $itemTotal = ($itemData['price'] * $itemData['quantity']) - ($itemData['discount'] ?? 0);
            $totalItemsValue += $itemTotal;
        }

        // Loop through the items sent from the frontend
        foreach ($request->items as $itemData) {
            $itemTotal = ($itemData['price'] * $itemData['quantity']) - ($itemData['discount'] ?? 0);
            
            // Calculate proportional customer payment for this item
            $itemCustomerPay = $totalItemsValue > 0 ? ($itemTotal / $totalItemsValue) * $receivedAmount : 0;
            
            // Calculate balance for this item (customer payment - item total)
            $itemBalance = $itemCustomerPay - $itemTotal;

            // Create the order item
            $orderItem = $order->items()->create([
                'price' => $itemData['price'], // Use the updated price
                'quantity' => $itemData['quantity'],
                'product_id' => $itemData['product_id'],
                'discount' => $itemData['discount'] ?? 0, // Save discount
                'customer_pay_amount' => round($itemCustomerPay, 2),
                'balance_amount' => round($itemBalance, 2),
            ]);

            // Retrieve the product using the product_id
            $product = Product::find($itemData['product_id']);

            // Check if the product exists and is of type 'product'
            if ($product && $product->productDetail->type === 'product') {
                $product->quantity -= $itemData['quantity']; // Decrease by the quantity ordered
                $product->save();
            }
        }

        // Clear the cart after creating the order
        $request->user()->cart()->detach();

        // Create the payment for the order
        $order->payments()->create([
            'amount' => $receivedAmount,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'order' => $order->load('customer'),
            'order_id' => $order->id,
        ]);
    }


    public function partialPayment(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $order = Order::with('items')->findOrFail($request->input('order_id'));
        $amount = round((float) $request->input('amount'), 2);

        $remainingAmount = round($order->total() - $order->receivedAmount(), 2);
        if ($amount - $remainingAmount > 0.009) {
            return redirect()
                ->route('orders.index')
                ->withErrors(['amount' => 'Amount exceeds remaining balance'])
                ->withInput($request->only(['order_id', 'amount']));
        }

        DB::transaction(function () use ($order, $amount) {
            $order->payments()->create([
                'amount' => $amount,
                'user_id' => auth()->id(),
            ]);

            $this->applyPaymentToOrderItems($order, $amount);
        });

        return redirect()
            ->route('orders.index')
            ->with('success', 'Partial payment of ' . config('settings.currency_symbol') . number_format($amount, 2) . ' recorded successfully.');
    }

    private function applyPaymentToOrderItems(Order $order, float $amount): void
    {
        $remaining = round($amount, 2);
        $lastUpdatedItem = null;

        $order->items->sortBy('id')->each(function ($item) use (&$remaining, &$lastUpdatedItem) {
            if ($remaining <= 0) {
                return false;
            }

            $itemTotal = ($item->price * $item->quantity) - ($item->discount ?? 0);
            $alreadyPaid = (float) ($item->customer_pay_amount ?? 0);
            $outstanding = round($itemTotal - $alreadyPaid, 2);

            if ($outstanding <= 0) {
                return true;
            }

            $applied = min($outstanding, $remaining);
            $updatedPaid = round($alreadyPaid + $applied, 2);

            $item->customer_pay_amount = $updatedPaid;
            $item->balance_amount = round($updatedPaid - $itemTotal, 2);
            $item->save();

            $remaining = round($remaining - $applied, 2);
            $lastUpdatedItem = $item;

            return true;
        });

        if ($remaining > 0 && $lastUpdatedItem) {
            $item = $lastUpdatedItem->fresh();
            $itemTotal = ($item->price * $item->quantity) - ($item->discount ?? 0);
            $item->customer_pay_amount = round(($item->customer_pay_amount ?? 0) + $remaining, 2);
            $item->balance_amount = round($item->customer_pay_amount - $itemTotal, 2);
            $item->save();
        }
    }
}
