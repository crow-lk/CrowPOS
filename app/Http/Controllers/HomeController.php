<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $storeId = auth()->user()->store_id;
        $orders = Order::with(['items', 'payments'])->where('store_id', $storeId)->get();
        $customers_count = Customer::count();

           $low_stock_products = Product::where('store_id', $storeId)->whereHas('productDetail', function($query) { $query->where('type', 'product');})->where('quantity', '<', 10)->get();


           $bestSellingProducts = Product::with(['productDetail', 'orderItems'])
            ->whereHas('productDetail', function ($query) {
                $query->where('type', 'product');
            })
            ->where('store_id', $storeId) // Filter by store_id
            ->select('products.*')
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->having('total_sold', '>', 10)
            ->get();



           $currentMonthBestSelling = Product::with(['productDetail', 'orderItems'])
                ->whereHas('productDetail', function ($query) {
                    $query->where('type', 'product');
                })
                ->where('store_id', $storeId)
                ->withCount(['orderItems as total_sold' => function ($query) {
                    // Filter order items to only those linked to orders in current year and month
                    $query->whereHas('order', function ($query) {
                        $query->whereYear('created_at', date('Y'))
                            ->whereMonth('created_at', date('m'));
                    })->select(DB::raw('SUM(quantity)'));
                }])
                ->having('total_sold', '>', 500)
                ->get();
            $pastSixMonthsHotProducts = Product::with(['productDetail', 'orderItems'])
                ->whereHas('productDetail', function ($query) {
                    $query->where('type', 'product');
                })
                ->where('store_id', $storeId)
                ->withCount(['orderItems as total_sold' => function ($query) {
                    // Filter order items to only those linked to orders created in last 6 months
                    $query->whereHas('order', function ($query) {
                        $query->where('created_at', '>=', now()->subMonths(6));
                    })->select(DB::raw('SUM(quantity)'));
                }])
                ->having('total_sold', '>', 1000)
                ->get();





        // Get outstanding customers (customers with negative balance amounts)
        $outstanding_customers = $this->getOutstandingCustomers($storeId);

        return view('home', [
            'orders_count' => $orders->count(),
            'income' => $orders->map(function ($i) {
                return $i->receivedAmount() > $i->total() ? $i->total() : $i->receivedAmount();
            })->sum(),
            'income_today' => $orders->where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->map(function ($i) {
                return $i->receivedAmount() > $i->total() ? $i->total() : $i->receivedAmount();
            })->sum(),
            'customers_count' => $customers_count,
            'low_stock_products' => $low_stock_products,
            'best_selling_products' => $bestSellingProducts,
            'current_month_products' => $currentMonthBestSelling,
            'past_months_products' => $pastSixMonthsHotProducts,
            'outstanding_customers' => $outstanding_customers,
        ]);
    }

    /**
     * Get customers with outstanding balances (negative balance amounts)
     *
     * @param int $storeId
     * @return \Illuminate\Support\Collection
     */
    private function getOutstandingCustomers($storeId)
    {
        return Customer::select([
            'customers.id',
            'customers.first_name',
            'customers.last_name',
            DB::raw('SUM(order_items.balance_amount) as total_outstanding')
        ])
        ->join('orders', 'customers.id', '=', 'orders.customer_id')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.store_id', $storeId)
        ->whereNotNull('order_items.balance_amount')
        ->groupBy('customers.id', 'customers.first_name', 'customers.last_name')
        ->havingRaw('SUM(order_items.balance_amount) < 0') // Only customers with negative balance (owing money)
        ->orderBy('total_outstanding', 'asc') // Most owing first
        ->limit(10) // Limit to top 10 customers
        ->get()
        ->map(function ($customer) {
            $customer->total_outstanding = abs($customer->total_outstanding); // Convert to positive for display
            $customer->full_name = $customer->first_name . ' ' . $customer->last_name;
            return $customer;
        });
    }
}
