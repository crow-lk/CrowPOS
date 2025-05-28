@extends('layouts.admin')

@section('title', __('Stock Movement List'))
@section('content-header', __('Stock Movement List'))

@section('content-actions')
<a href="{{ route('stock_movements.create') }}" class="btn btn-primary">{{ __('Add Stock Movement') }}</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('Stock Out & Stock In') }}</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle shadow-lg rounded"
                style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; overflow: hidden; width: 100%;">

                <!-- Table Head -->
                <thead style="background: #2C3E50; color: white;">
                    <tr>
                        <th class="text-center px-4 py-3" style="border-top-left-radius: 12px;">{{ __('ID') }}</th>
                        <th class="text-start px-4 py-3">{{ __('Movement Type') }}</th>
                        <th class="text-start px-4 py-3">{{ __('Supplier Name') }}</th>
                        <th class="text-right px-4 py-3">{{ __('Total Cost Price') }}</th>
                        <th class="text-center px-4 py-3">{{ __('Created At') }}</th>
                        <th class="text-center px-4 py-3" style="border-top-right-radius: 12px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody>
                    @foreach ($stockMovements as $stockMovement)
                    @if($stockMovement->movement_type != 'adjustment')
                    <tr class="transition"
                        style="border-bottom: 1px solid rgba(255, 255, 255, 0.2); transition: background 0.3s ease-in-out;">
                        <td class="text-center fw-bold px-4 py-3">{{ $stockMovement->id }}</td>
                        <td class="text-start fw-semibold px-4 py-3">
                            @if($stockMovement->movement_type == 'stock_in')
                                <span class="badge bg-success">Stock In</span>
                            @elseif($stockMovement->movement_type == 'stock_out')
                                <span class="badge bg-danger">Stock Out</span>
                            @elseif($stockMovement->movement_type == 'adjustment')
                                <span class="badge bg-warning text-dark">Adjustment</span>
                            @endif
                        </td>
                        <td class="text-start fw-semibold px-4 py-3">{{ $stockMovement->supplier->first_name ?? 'N/A' }} {{ $stockMovement->supplier->last_name ?? '' }}</td>
                        <td class="fw-bold px-4 py-3 text-right">
                            @php
                                // Decode the JSON and ensure it's an array
                                $costprices = json_decode($stockMovement->cost_prices, true); // true for associative array
                                $costPriceSum = is_array($costprices) ? array_sum($costprices) : 0; // Check if it's an array
                            @endphp
                            {{ number_format($costPriceSum, 2) }} <!-- Format the sum to 2 decimal places -->
                        </td>
                        <td class="text-center fw-semibold px-4 py-3">{{ $stockMovement->created_at->format('Y-m-d') }}</td>
                        <td class="text-center px-4 py-3">
                            <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px;">
                                <button class="btn btn-sm px-3 py-1 shadow-sm rounded-pill btn-view" data-details="{{ json_encode($stockMovement) }}" style="background: #1fa4f1; color: white; border: none;">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm px-3 py-1 shadow-sm rounded-pill btn-delete"
                                    data-url="{{ route('stock_movements.destroy', $stockMovement) }}"
                                    style="background: #e74c3c; color: white; border: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $stockMovements->render() }}
    </div>
</div>

<!-- Adjustments Table -->
<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">{{ __('Adjustments') }}</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle shadow-lg rounded"
                style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; overflow: hidden; width: 100%;">

                <!-- Adjustments Table Head -->
                <thead style="background: #2C3E50; color: white;">
                    <tr>
                        <th class="text-center px-4 py-3" style="border-top-left-radius: 12px;">{{ __('ID') }}</th>
                        <th class="text-start px-4 py-3">{{ __('Movement Type') }}</th>
                        <th class="text-start px-4 py-3">{{ __('From') }}</th>
                        <th class="text-start px-4 py-3">{{ __('To') }}</th>
                        <th class="text-right px-4 py-3">{{ __('Total Cost Price') }}</th>
                        <th class="text-center px-4 py-3">{{ __('Created At') }}</th>
                        <th class="text-center px-4 py-3" style="border-top-right-radius: 12px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>

                <!-- Adjustments Table Body -->
                <tbody>
                    @foreach ($stockMovements as $stockMovement)
                        @if($stockMovement->movement_type == 'adjustment')
                        <tr class="transition"
                            style="border-bottom: 1px solid rgba(255, 255, 255, 0.2); transition: background 0.3s ease-in-out;">
                            <td class="text-center fw-bold px-4 py-3">{{ $stockMovement->id }}</td>
                            <td class="text-start fw-semibold px-4 py-3">
                                <span class="badge bg-warning text-dark">Adjustment</span>
                            </td>
                            <td class="text-start fw-semibold px-4 py-3">{{ $stockMovement->fromStore->name?? 'N/A' }}</td>
                            <td class="text-start fw-semibold px-4 py-3">{{ $stockMovement->toStore->name ?? 'N/A' }}</td>
                            <td class="fw-bold px-4 py-3 text-right">
                                @php
                                    // Decode the JSON and ensure it's an array
                                    $costprices = json_decode($stockMovement->cost_prices, true); // true for associative array
                                    $costPriceSum = is_array($costprices) ? array_sum($costprices) : 0; // Check if it's an array
                                @endphp
                                {{ number_format($costPriceSum, 2) }} <!-- Format the sum to 2 decimal places -->
                            </td>
                            <td class="text-center fw-semibold px-4 py-3">{{ $stockMovement->created_at->format('Y-m-d') }}</td>
                            <td class="text-center px-4 py-3">
                                <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px;">
                                    <button class="btn btn-sm px-3 py-1 shadow-sm rounded-pill btn-view" data-details="{{ json_encode($stockMovement) }}" style="background: #1fa4f1; color: white; border: none;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm px-3 py-1 shadow-sm rounded-pill btn-delete"
                                        data-url="{{ route('stock_movements.destroy', $stockMovement) }}"
                                        style="background: #e74c3c; color: white; border: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $stockMovements->render() }}
    </div>
</div>

@endsection
@section('model')
<!-- Modal -->
<div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInvoiceLabel">Stock Movement Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('Product Name') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Price') }}</th>
                        </tr>
                    </thead>
                    <tbody id="productDetailsBody">
                        <!-- Dynamic content will be injected here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
@php
    $productIds = json_decode($stockMovement->products);
    $productNames = \App\Models\Product::whereIn('id', $productIds)->pluck('name')->toArray();
@endphp

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script type="module">
    $(document).ready(function() {
        // Delete button functionality
        $(document).on('click', '.btn-delete', function() {
            var $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: '{{ __('stockMovement.sure') }}',
                text: '{{ __('stockMovement.really_delete') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('stockMovement.yes_delete') }}',
                cancelButtonText: '{{ __('stockMovement.No') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        $this.closest('tr').fadeOut(500, function() {
                            $(this).remove();
                        });
                    });
                }
            });
        });

        // View button functionality
        $(document).on('click', '.btn-view', function() {
            var details = $(this).data('details');
            var productDetailsBody = $('#productDetailsBody');
            productDetailsBody.empty(); // Clear previous content

            // Populate the modal with product details
            if (details.products) {
                var productIds = JSON.parse(details.products);
                var quantities = JSON.parse(details.quantities); // Assuming you have quantities in the stock movement
                var prices = JSON.parse(details.cost_prices); // Assuming you have cost prices in the stock movement

                var productNames = @json($productNames);

                productIds.forEach(function(productId, index) {
                    var productName = productNames[productId-1];

                    productDetailsBody.append(`
                        <tr>
                            <td>${productName}</td>
                            <td>${quantities[index]}</td>
                            <td>${prices[index]}</td>
                        </tr>
                    `);
                });
            }

            // Show the modal
            $('#modalInvoice').modal('show');
        });
    });
</script>

@endsection
