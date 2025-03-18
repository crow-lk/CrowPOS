@extends('layouts.admin')

@section('title', __('product.Product_List'))
@section('content-header', __('product.Product_List'))
@section('content-actions')
<a href="{{route('products.create')}}" class="btn btn-primary">{{ __('product.Create_Product') }}</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card product-list">
    <div class="card-body">
    <div class="table-responsive">
    <table class="table table-hover align-middle shadow-lg rounded"
        style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; overflow: hidden; width: 100%;">
        
        <!-- Table Head -->
        <thead style="background: #2C3E50; color: white;">
            <tr>
                <th class="text-center px-4 py-3" style="border-top-left-radius: 12px;">#</th>
                <th class="text-start px-4 py-3">{{ __('product.Name') }}</th>
                <th class="text-center px-4 py-3">{{ __('product.Image') }}</th>
                <th class="text-center px-4 py-3">{{ __('product.Barcode') }}</th>
                <th class="text-center px-4 py-3">{{ __('product.Price') }}</th>
                <th class="text-center px-4 py-3">{{ __('product.Quantity') }}</th>
                <th class="text-center px-4 py-3">{{ __('product.Status') }}</th>
                <th class="text-center px-4 py-3">{{ __('product.Created_At') }}</th>
                <th class="text-center px-4 py-3">{{ __('product.Updated_At') }}</th>
                <th class="text-center px-4 py-3" style="border-top-right-radius: 12px;">{{ __('product.Actions') }}</th>
            </tr>
        </thead>

        <!-- Table Body -->
        <tbody>
            @foreach ($products as $product)
            <tr class="transition"
                style="border-bottom: 1px solid rgba(255, 255, 255, 0.2); transition: background 0.3s ease-in-out;">
                
                <td class="text-center fw-bold px-4 py-3 text-primary">{{ $product->id }}</td>
                <td class="text-start fw-bold px-4 py-3 text-secondary text-muted">{{ $product->name }}</td>
                <td class="text-center px-4 py-3">
                    <img src="{{ Storage::url($product->image) }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                </td>
                <td class="text-center px-4 py-3 text-muted">{{ $product->barcode }}</td>
                <td class="text-center px-4 py-3 fw-bold text-success">{{ number_format($product->price, 2) }}</td>
                <td class="text-center px-4 py-3">{{ $product->quantity }}</td>
                <td class="text-center px-4 py-3">
                    <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                        {{ $product->status ? __('common.Active') : __('common.Inactive') }}
                    </span>
                </td>
                <td class="text-center px-4 py-3 text-muted">{{ $product->created_at->format('Y-m-d') }}</td>
                <td class="text-center px-4 py-3 text-muted">{{ $product->updated_at->format('Y-m-d') }}</td>
                <td class="text-center px-4 py-3">
    <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px;">
        <a href="{{ route('products.edit', $product) }}" 
            class="btn btn-sm px-3 py-1 shadow-sm rounded-pill"
            style="background: #2980b9; color: white; border: none;">
            <i class="fas fa-edit"></i> 
        </a>
        <button class="btn btn-sm px-3 py-1 shadow-sm rounded-pill btn-delete"
            data-url="{{ route('products.destroy', $product) }}"
            style="background: #e74c3c; color: white; border: none;">
            <i class="fas fa-trash"></i> 
        </button>
    </div>
</td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>


        {{ $products->render() }}
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
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false,
            })

            swalWithBootstrapButtons.fire({
                title: '{{ __('product.sure') }}', // Wrap in quotes
                text: '{{ __('product.really_delete') }}', // Wrap in quotes
                icon: 'warning', // Fix the icon string
                showCancelButton: true,
                confirmButtonText: '{{ __('product.yes_delete') }}', // Wrap in quotes
                cancelButtonText: '{{ __('product.No') }}', // Wrap in quotes
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}' // Wrap in quotes
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
