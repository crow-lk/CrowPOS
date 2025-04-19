@extends('layouts.admin')
@section('content-header', __('dashboard.title'))
@section('content')
<div class="container-fluid">
   <!-- Small Boxes -->
   <div class="row">
      <!-- Orders Count -->
      <div class="col-lg-3 col-6">
         <div class="small-box bg-gradient-info shadow-sm">
            <div class="inner">
               <h3 class="font-weight-bolder">{{$orders_count}}</h3>
               <p>{{ __('dashboard.Orders_Count') }}</p>
            </div>
            <div class="icon">
               <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>

      <!-- Income -->
      <div class="col-lg-3 col-6">
         <div class="small-box bg-gradient-success shadow-sm">
            <div class="inner">
               <h3 class="font-weight-bolder">{{config('settings.currency_symbol')}} {{number_format($income, 2)}}</h3>
               <p>{{ __('dashboard.Income') }}</p>
            </div>
            <div class="icon">
               <i class="fas fa-dollar-sign"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>

      <!-- Income Today -->
      <div class="col-lg-3 col-6">
         <div class="small-box bg-gradient-danger shadow-sm">
            <div class="inner">
               <h3 class="font-weight-bolder">{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</h3>
               <p>{{ __('dashboard.Income_Today') }}</p>
            </div>
            <div class="icon">
               <i class="fas fa-coins"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>

      <!-- Customers Count -->
      <div class="col-lg-3 col-6">
         <div class="small-box bg-gradient-warning shadow-sm">
            <div class="inner">
               <h3 class="font-weight-bolder">{{$customers_count}}</h3>
               <p>{{ __('dashboard.Customers_Count') }}</p>
            </div>
            <div class="icon">
               <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('customers.index') }}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
   </div>

   <!-- Low Stock Products -->
   <div class="row mt-4">
      <div class="col-12 col-md-6">
         <div class="card shadow-sm">
            <div class="card-header bg-primary d-flex align-items-center">
               <h4 class="card-title mb-0 text-white"><i class="fas fa-exclamation-triangle mr-2"></i> {{ __('dashboard.Low_Stock_Products') }}</h4>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <table class="table table-hover table-striped rounded overflow-hidden">
                     <thead class="bg-light">
                        <tr>
                           <th>Name</th>
                           <th>Quantity</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($low_stock_products as $product)
                        <tr data-toggle="modal" data-target="#productModalLow{{$product->id}}" style="cursor: pointer;">
                           <td>{{$product->name}}</td>
                           <td>{{$product->quantity}}</td>
                        </tr>

                        <!-- Modal for Product Details -->
                        <div class="modal fade" id="productModalLow{{$product->id}}" tabindex="-1" aria-labelledby="productModalLabelLow{{$product->id}}" aria-hidden="true">
                           <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                 <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white" id="productModalLabelLow{{$product->id}}">{{$product->name}}</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                       <span aria-hidden="true">&times;</span>
                                    </button>
                                 </div>
                                 <div class="modal-body">
                                    <div class="text-center mb-3">
                                       <img src="{{ Storage::url($product->image) }}" alt="{{$product->name}}" class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                    <p><strong>Price:</strong> {{config('settings.currency_symbol')}} {{$product->price}}</p>
                                    <p><strong>Quantity:</strong> {{$product->quantity}}</p>
                                    <p><strong>Status:</strong>
                                       <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                          {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                       </span>
                                    </p>
                                    <p><strong>Barcode:</strong> {{$product->barcode}}</p>
                                    <p><strong>Updated At:</strong> {{$product->updated_at->format('d M Y')}}</p>
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>

      <!-- Hot Products of the Year -->
      <div class="col-12 col-md-6">
         <div class="card shadow-sm">
            <div class="card-header bg-gradient-warning d-flex align-items-center">
               <h4 class="card-title mb-0 text-black"><i class="fas fa-trophy mr-2"></i> {{ __('dashboard.Hot_Products_Year') }}</h4>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <table class="table table-hover table-striped rounded overflow-hidden">
                     <thead class="bg-light">
                        <tr>
                           <th>Name</th>
                           <th>Quantity</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($past_months_products as $product)
                        <tr data-toggle="modal" data-target="#productModalYear{{$product->id}}" style="cursor: pointer;">
                           <td>{{$product->name}}</td>
                           <td>{{$product->quantity}}</td>
                        </tr>

                        <!-- Modal for Product Details -->
                        <div class="modal fade" id="productModalYear{{$product->id}}" tabindex="-1" aria-labelledby="productModalLabelYear{{$product->id}}" aria-hidden="true">
                           <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                 <div class="modal-header bg-gradient-warning">
                                    <h5 class="modal-title text-black" id="productModalLabelYear{{$product->id}}">{{$product->name}}</h5>
                                    <button type="button" class="close text-black" data-dismiss="modal" aria-label="Close">
                                       <span aria-hidden="true">&times;</span>
                                    </button>
                                 </div>
                                 <div class="modal-body">
                                    <div class="text-center mb-3">
                                       <img src="{{ Storage::url($product->image) }}" alt="{{$product->name}}" class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                    <p><strong>Price:</strong> {{config('settings.currency_symbol')}} {{$product->price}}</p>
                                    <p><strong>Quantity:</strong> {{$product->quantity}}</p>
                                    <p><strong>Status:</strong>
                                       <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                          {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                       </span>
                                    </p>
                                    <p><strong>Barcode:</strong> {{$product->barcode}}</p>
                                    <p><strong>Updated At:</strong> {{$product->updated_at->format('d M Y')}}</p>
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Other Sections (Hot Products, Best Selling Products) -->
   <div class="row mt-4">
      <div class="col-12 col-md-6">
         <div class="card shadow-sm">
            <div class="card-header bg-success d-flex align-items-center">
               <h4 class="card-title mb-0 text-white"><i class="fas fa-fire mr-2"></i> {{ __('dashboard.Hot_Products') }}</h4>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <table class="table table-hover table-striped rounded overflow-hidden">
                     <thead class="bg-light">
                        <tr>
                           <th>Name</th>
                           <th>Quantity</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($current_month_products as $product)
                        <tr data-toggle="modal" data-target="#productModalHot{{$product->id}}" style="cursor: pointer;">
                           <td>{{$product->name}}</td>
                           <td>{{$product->quantity}}</td>
                        </tr>

                        <!-- Modal for Product Details -->
                        <div class="modal fade" id="productModalHot{{$product->id}}" tabindex="-1" aria-labelledby="productModalLabelHot{{$product->id}}" aria-hidden="true">
                           <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                 <div class="modal-header bg-success">
                                    <h5 class="modal-title text-white" id="productModalLabelHot{{$product->id}}">{{$product->name}}</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                       <span aria-hidden="true">&times;</span>
                                    </button>
                                 </div>
                                 <div class="modal-body">
                                    <div class="text-center mb-3">
                                       <img src="{{ Storage::url($product->image) }}" alt="{{$product->name}}" class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                    <p><strong>Price:</strong> {{config('settings.currency_symbol')}} {{$product->price}}</p>
                                    <p><strong>Quantity:</strong> {{$product->quantity}}</p>
                                    <p><strong>Status:</strong>
                                       <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                          {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                       </span>
                                    </p>
                                    <p><strong>Barcode:</strong> {{$product->barcode}}</p>
                                    <p><strong>Updated At:</strong> {{$product->updated_at->format('d M Y')}}</p>
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>

      <div class="col-12 col-md-6">
         <div class="card shadow-sm">
            <div class="card-header bg-danger d-flex align-items-center">
               <h4 class="card-title mb-0 text-white"><i class="fas fa-star mr-2"></i> {{ __('dashboard.Best_Selling_Products') }}</h4>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <table class="table table-hover table-striped rounded overflow-hidden">
                     <thead class="bg-light">
                        <tr>
                           <th>Name</th>
                           <th>Quantity</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($best_selling_products as $product)
                        <tr data-toggle="modal" data-target="#productModalBest{{$product->id}}" style="cursor: pointer;">
                           <td>{{$product->name}}</td>
                           <td>{{$product->quantity}}</td>
                        </tr>

                        <!-- Modal for Product Details -->
                        <div class="modal fade" id="productModalBest{{$product->id}}" tabindex="-1" aria-labelledby="productModalLabelBest{{$product->id}}" aria-hidden="true">
                           <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                 <div class="modal-header bg-danger">
                                    <h5 class="modal-title text-white" id="productModalLabelBest{{$product->id}}">{{$product->name}}</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                       <span aria-hidden="true">&times;</span>
                                    </button>
                                 </div>
                                 <div class="modal-body">
                                    <div class="text-center mb-3">
                                       <img src="{{ Storage::url($product->image) }}" alt="{{$product->name}}" class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                    <p><strong>Price:</strong> {{config('settings.currency_symbol')}} {{$product->price}}</p>
                                    <p><strong>Quantity:</strong> {{$product->quantity}}</p>
                                    <p><strong>Status:</strong>
                                       <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                          {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                       </span>
                                    </p>
                                    <p><strong>Barcode:</strong> {{$product->barcode}}</p>
                                    
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
