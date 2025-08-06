<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Date range filtering
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();
        
        // Sales Summary
        $salesQuery = Order::where('store_id', $user->store_id)
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
        
        $totalSales = $salesQuery->sum(DB::raw('(SELECT SUM(quantity * price) FROM order_items WHERE order_items.order_id = orders.id)'));
        $totalOrders = $salesQuery->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Top Selling Products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_details', 'products.product_detail_id', '=', 'product_details.id')
            ->where('orders.store_id', $user->store_id)
            ->whereBetween('orders.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->select('product_details.name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'))
            ->groupBy('products.id', 'product_details.name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();
        
        // Monthly Sales Chart Data
        $monthlySales = Order::where('store_id', $user->store_id)
            ->whereBetween('created_at', [Carbon::now()->subMonths(11)->startOfMonth(), Carbon::now()->endOfMonth()])
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM((SELECT SUM(quantity * price) FROM order_items WHERE order_items.order_id = orders.id)) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Low Stock Products
        $lowStockProducts = Product::where('store_id', $user->store_id)
            ->where('quantity', '<=', 10)
            ->with('productDetail')
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get();
        
        // Customer Statistics
        $totalCustomers = Customer::count();
        $newCustomersThisMonth = Customer::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
        
        // Outstanding Orders (orders with partial payments or items with balance_amount)
        $outstandingOrders = Order::where('store_id', $user->store_id)
            ->with(['customer', 'payments', 'items'])
            ->whereHas('items', function($query) {
                $query->whereNotNull('balance_amount');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($order) {
                // Check if order has items with balance_amount or partial payments
                $hasBalanceAmount = $order->items->where('balance_amount', '!=', null)->count() > 0;
                $totalAmount = $order->total();
                $receivedAmount = $order->receivedAmount();
                $hasPartialPayment = $receivedAmount > 0 && $receivedAmount < $totalAmount;
                
                return $hasBalanceAmount || $hasPartialPayment;
            })
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->getCustomerName(),
                    'customer_id' => $order->customer_id,
                    'total_amount' => $order->total(),
                    'received_amount' => $order->receivedAmount(),
                    'outstanding_amount' => $order->total() - $order->receivedAmount(),
                    'created_at' => $order->created_at,
                    'payments' => $order->payments->sortByDesc('created_at')->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'amount' => $payment->amount,
                            'created_at' => $payment->created_at,
                        ];
                    })->values(),
                ];
            });
        
        return view('reports.index', compact(
            'totalSales',
            'totalOrders',
            'averageOrderValue',
            'topProducts',
            'monthlySales',
            'lowStockProducts',
            'totalCustomers',
            'newCustomersThisMonth',
            'outstandingOrders',
            'startDate',
            'endDate'
        ));
    }


}