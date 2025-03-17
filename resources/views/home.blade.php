@extends('layouts.admin')
@section('content-header', __('dashboard.title'))
@section('content')

<div class="container-fluid">
   <div class="row">
      <!-- Small Boxes -->
      <div class="col-lg-3 col-6">
         <div class="small-box bg-info">
            <div class="inner">
               <h3>{{$orders_count}}</h3>
               <p>{{ __('dashboard.Orders_Count') }}</p>
            </div>
            <div class="icon">
               <i class="ion ion-bag"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
      <div class="col-lg-3 col-6">
         <div class="small-box bg-success">
            <div class="inner">
               <h3>{{config('settings.currency_symbol')}} {{number_format($income, 2)}}</h3>
               <p>{{ __('dashboard.Income') }}</p>
            </div>
            <div class="icon">
               <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
      <div class="col-lg-3 col-6">
         <div class="small-box bg-danger">
            <div class="inner">
               <h3>{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</h3>
               <p>{{ __('dashboard.Income_Today') }}</p>
            </div>
            <div class="icon">
               <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
      <div class="col-lg-3 col-6">
         <div class="small-box bg-warning">
            <div class="inner">
               <h3>{{$customers_count}}</h3>
               <p>{{ __('dashboard.Customers_Count') }}</p>
            </div>
            <div class="icon">
               <i class="ion ion-person-add"></i>
            </div>
            <a href="{{ route('customers.index') }}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
   </div>

   <!-- Dropdown to Select Table -->
   <div class="container-fluid mt-4">
      <div class="row">
         <div class="col-12">
            <label for="tableSelector" class="form-label"><strong>Show Statictics:</strong></label>
            <select id="tableSelector" class="form-select" onchange="showSelectedTable()">
               <option value="">-- Select a Table --</option>
               <option value="lowStockTable">Low Stock Products</option>
               <option value="hotProductsTable">Hot Products</option>
               <option value="yearlyHotProductsTable">Hot Products of the Year</option>
               <option value="bestSellingProductsTable">Best Selling Products</option>
            </select>
         </div>
      </div>
   </div>

   <!-- Tables -->
   <div class="container-fluid mt-4">
      <div class="row">
         <!-- Low Stock Products -->
         <div class="col-12" id="lowStockTable" style="display: none;">
            <h3>Low Stock Products</h3>
            <section class="content">
               <div class="card product-list">
                  <div class="card-body">
                     <table class="table">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>Price</th>
                              <th>Quantity</th>
                              <th>Status</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach ($low_stock_products as $product)
                           <tr>
                              <td>{{$product->name}}</td>
                              <td>{{$product->price}}</td>
                              <td>{{$product->quantity}}</td>
                              <td>
                                 <span class="right badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                    {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                 </span>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
            </section>
         </div>

         <!-- Hot Products -->
         <div class="col-12" id="hotProductsTable" style="display: none;">
            <h3>Hot Products</h3>
            <section class="content">
               <div class="card product-list">
                  <div class="card-body">
                     <table class="table">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>Price</th>
                              <th>Quantity</th>
                              <th>Status</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach ($current_month_products as $product)
                           <tr>
                              <td>{{$product->name}}</td>
                              <td>{{$product->price}}</td>
                              <td>{{$product->quantity}}</td>
                              <td>
                                 <span class="right badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                    {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                 </span>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
            </section>
         </div>

         <!-- Hot Products of the Year -->
         <div class="col-12" id="yearlyHotProductsTable" style="display: none;">
            <h3>Hot Products of the Year</h3>
            <section class="content">
               <div class="card product-list">
                  <div class="card-body">
                     <table class="table">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>Price</th>
                              <th>Quantity</th>
                              <th>Status</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach ($past_months_products as $product)
                           <tr>
                              <td>{{$product->name}}</td>
                              <td>{{$product->price}}</td>
                              <td>{{$product->quantity}}</td>
                              <td>
                                 <span class="right badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                    {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                 </span>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
            </section>
         </div>

         <!-- Best Selling Products -->
         <div class="col-12" id="bestSellingProductsTable" style="display: none;">
            <h3>Best Selling Products</h3>
            <section class="content">
               <div class="card product-list">
                  <div class="card-body">
                     <table class="table">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>Price</th>
                              <th>Quantity</th>
                              <th>Status</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach ($best_selling_products as $product)
                           <tr>
                              <td>{{$product->name}}</td>
                              <td>{{$product->price}}</td>
                              <td>{{$product->quantity}}</td>
                              <td>
                                 <span class="right badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                    {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                 </span>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
            </section>
         </div>
      </div>
   </div>
</div>

<!-- JavaScript for Dropdown Selection -->
<script>
function showSelectedTable() {
    // Get the selected value from the dropdown
    const selectedValue = document.getElementById('tableSelector').value;

    // Hide all tables
    const tables = ['lowStockTable', 'hotProductsTable', 'yearlyHotProductsTable', 'bestSellingProductsTable'];
    tables.forEach(tableId => {
        const table = document.getElementById(tableId);
        if (table) {
            table.style.display = 'none';
        }
    });

    // Show the selected table
    if (selectedValue) {
        const selectedTable = document.getElementById(selectedValue);
        if (selectedTable) {
            selectedTable.style.display = 'block';
        }
    }
}
</script>

@endsection
