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
        $orders = new Order();
        if ($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $orders = $orders->with(['items.product', 'payments', 'customer', 'store', 'items.product.productDetail'])->where('store_id', $user->store_id)->latest()->paginate(10);

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
        // return $request;
        $orderId = $request->order_id;
        $amount = $request->amount;

        // Find the order
        $order = Order::findOrFail($orderId);

        // Check if the amount exceeds the remaining balance
        $remainingAmount = $order->total() - $order->receivedAmount();
        if ($amount > $remainingAmount) {
            return redirect()->route('orders.index')->withErrors('Amount exceeds remaining balance');
        }

        // Save the payment
        DB::transaction(function () use ($order, $amount) {
            $order->payments()->create([
                'amount' => $amount,
                'user_id' => auth()->user()->id,
            ]);
        });

        return redirect()->route('orders.index')->with('success', 'Partial payment of ' . config('settings.currency_symbol') . number_format($amount, 2) . ' made successfully.');
    }
}
