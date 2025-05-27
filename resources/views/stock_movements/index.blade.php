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
                        <th class="text-start px-4 py-3">{{ __('Product Names') }}</th>
                        <th class="text-start px-4 py-3">{{ __('Supplier Name') }}</th>
                        <th class="text-center px-4 py-3">{{ __('Quantity') }}</th>
                        <th class="text-right px-4 py-3">{{ __('Cost Price') }}</th>
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
                        <td class="text-start fw-semibold px-4 py-3">
                            @php
                                $productIds = json_decode($stockMovement->products);
                                $productNames = \App\Models\Product::whereIn('id', $productIds)->pluck('name')->toArray();
                            @endphp
                            {{ implode(', ', $productNames) }}
                        </td>
                        <td class="text-start fw-semibold px-4 py-3">{{ $stockMovement->supplier->first_name ?? 'N/A' }} {{ $stockMovement->supplier->last_name ?? '' }}</td>
                        <td class="text-center fw-bold px-4 py-3">{{ $stockMovement->quantity }}</td>
                        <td class="fw-bold px-4 py-3 text-right">{{ $stockMovement->cost_price}}</td>
                        <td class="text-center px-4 py-3">
                            <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px;">
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
                        <th class="text-start px-4 py-3">{{ __('Product Names') }}</th>
                        <th class="text-start px-4 py-3">{{ __('From') }}</th>
                        <th class="text-start px-4 py-3">{{ __('To') }}</th>
                        <th class="text-center px-4 py-3">{{ __('Quantity') }}</th>
                        <th class="text-right px-4 py-3">{{ __('Cost Price') }}</th>
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
                            <td class="text-start fw-semibold px-4 py-3">
                                @php
                                    $productIds = json_decode($stockMovement->products);
                                    $productNames = \App\Models\Product::whereIn('id', $productIds)->pluck('name')->toArray();
                                @endphp
                                {{ implode(', ', $productNames) }}
                            </td>
                            <td class="text-start fw-semibold px-4 py-3">{{ $stockMovement->fromStore->name?? 'N/A' }}</td>
                            <td class="text-start fw-semibold px-4 py-3">{{ $stockMovement->toStore->name ?? 'N/A' }}</td>
                            <td class="text-center fw-bold px-4 py-3">{{ $stockMovement->quantity }}</td>
                            <td class="fw-bold px-4 py-3 text-right">{{ $stockMovement->cost_price}}</td>
                            <td class="text-center px-4 py-3">
                                <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px;">
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

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script type="module">
    $(document).ready(function() {
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
                showCancelButton: true ,
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
    });
</script>
@endsection
