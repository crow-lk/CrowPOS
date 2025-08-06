@extends('layouts.admin')

@section('title', 'Reports')
@section('content-header', 'Reports Dashboard')

@section('content')
{{-- Commented out for now - can be enabled when needed --}}
{{-- 
<div class="row">
    <!-- Date Filter Card -->
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('reports.index') }}" method="GET">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', $startDate) }}" />
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', $endDate) }}" />
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100" type="submit">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sales Summary Cards -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>${{ number_format($totalSales, 2) }}</h3>
                <p>Total Sales</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalOrders }}</h3>
                <p>Total Orders</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>${{ number_format($averageOrderValue, 2) }}</h3>
                <p>Average Order Value</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalCustomers }}</h3>
                <p>Total Customers</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Selling Products -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top Selling Products</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->total_sold }}</td>
                                <td>${{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Low Stock Alert</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                            <tr>
                                <td>{{ $product->productDetail->name ?? 'N/A' }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>
                                    @if($product->quantity <= 5)
                                        <span class="badge bg-danger">Critical</span>
                                    @elseif($product->quantity <= 10)
                                        <span class="badge bg-warning">Low</span>
                                    @else
                                        <span class="badge bg-success">Normal</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Monthly Sales Chart -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Monthly Sales Trend</h3>
            </div>
            <div class="card-body">
                <canvas id="salesChart" width="400" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Additional Statistics -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Customer Statistics</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success">
                                <i class="fas fa-caret-up"></i> {{ $newCustomersThisMonth }}
                            </span>
                            <h5 class="description-header">{{ $totalCustomers }}</h5>
                            <span class="description-text">TOTAL CUSTOMERS</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-info">
                                <i class="fas fa-calendar"></i> This Month
                            </span>
                            <h5 class="description-header">{{ $newCustomersThisMonth }}</h5>
                            <span class="description-text">NEW CUSTOMERS</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('orders.index') }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-shopping-bag"></i> View Orders
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('products.index') }}" class="btn btn-success btn-block mb-2">
                            <i class="fas fa-cubes"></i> Manage Products
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('customers.index') }}" class="btn btn-info btn-block mb-2">
                            <i class="fas fa-users"></i> View Customers
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('cart.index') }}" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-cart-plus"></i> New Sale
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
--}}

<!-- Outstanding Orders Section -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Outstanding Orders</h3>
                <div class="card-tools">
                    <span class="badge badge-warning">{{ $outstandingOrders->total() }} Orders with Partial Payments</span>
                </div>
            </div>
            <div class="card-body">
                @if($outstandingOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-lg rounded"
                            style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; overflow: hidden; width: 100%;">
                            <thead style="background: #2C3E50; color: white;">
                                <tr>
                                    <th class="text-center px-4 py-3" style="border-top-left-radius: 12px;">Order ID</th>
                                    <th class="text-start px-4 py-3">Customer Name</th>
                                    <th class="text-center px-4 py-3">Total Amount</th>
                                    <th class="text-center px-4 py-3">Received Amount</th>
                                    <th class="text-center px-4 py-3">Outstanding</th>
                                    <th class="text-center px-4 py-3" style="border-top-right-radius: 12px;">Order Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outstandingOrders as $order)
                                <tr class="transition" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                                    <td class="text-center px-4 py-3">
                                        <strong style="color: #3498DB;">#{{ $order['id'] }}</strong>
                                    </td>
                                    <td class="text-start px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle text-primary me-2" style="font-size: 1.2em;"></i>
                                            <strong>{{ $order['customer_name'] }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center px-4 py-3">
                                        ${{ number_format($order['total_amount'], 2) }}
                                    </td>
                                    <td class="text-center px-4 py-3">
                                        ${{ number_format($order['received_amount'], 2) }}
                                    </td>
                                    <td class="text-center px-4 py-3">
                                        ${{ number_format($order['outstanding_amount'], 2) }}
                                    </td>
                                    <td class="text-center px-4 py-3">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($order['created_at'])->format('M d, Y') }}
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <p class="text-muted mb-0">
                                Showing {{ $outstandingOrders->firstItem() }} to {{ $outstandingOrders->lastItem() }} 
                                of {{ $outstandingOrders->total() }} results
                            </p>
                        </div>
                        <div>
                            {{ $outstandingOrders->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 3em;"></i>
                        <h4 class="mt-3">No Outstanding Orders</h4>
                        <p class="text-muted">All orders are fully paid or have no payments yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>



@endsection