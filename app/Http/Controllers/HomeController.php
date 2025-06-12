<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
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


           $bestSellingProducts = DB::table('products')
            ->join('product_details', 'product_details.id', '=', 'products.product_detail_id')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('product_details.type', 'product')
            ->where('products.store_id', $storeId) // Filter by store_id
            ->select('products.*', DB::raw('SUM(order_items.quantity) AS total_sold'))
            ->groupBy('products.id')
            ->havingRaw('SUM(order_items.quantity) > 10')
            ->get();


           $currentMonthBestSelling = DB::table('products')
            ->join('product_details', 'product_details.id', '=', 'products.product_detail_id')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('product_details.type', 'product')
            ->where('products.store_id', $storeId) // Filter by store_id
            ->select('products.*', DB::raw('SUM(order_items.quantity) AS total_sold'))
            ->whereYear('orders.created_at', date('Y'))
            ->whereMonth('orders.created_at', date('m'))
            ->groupBy('products.id')
            ->havingRaw('SUM(order_items.quantity) > 500')
            ->get();


        $pastSixMonthsHotProducts = DB::table('products')
            ->join('product_details', 'product_details.id', '=', 'products.product_detail_id')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('product_details.type', 'product')
            ->where('products.store_id', $storeId) // Filter by store_id
            ->where('orders.created_at', '>=', now()->subMonths(6))
            ->select('products.*', DB::raw('SUM(order_items.quantity) AS total_sold'))
            ->groupBy('products.id')
            ->havingRaw('SUM(order_items.quantity) > 1000')
            ->get();





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
        ]);
    }
}
