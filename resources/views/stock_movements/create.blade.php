@extends('layouts.admin')

@section('title', __('stockMovement.Create_stockMovement'))
@section('content-header', __('stockMovement.Create_stockMovement'))

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('stock_movements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="movement_type">Movement Type</label>
                    <select name="movement_type" id="movement_type"
                        class="form-control @error('movement_type') is-invalid @enderror">
                        <option value="">Select Type</option>
                        <option value="stock_in">Stock In</option>
                        <option value="stock_out">Stock Out</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                    @error('movement_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group" id="supplier-group">
                    <label for="supplier_id">{{ __('stockMovement.supplier') }}</label>
                    <select name="supplier_id" id="supplier_id"
                        class="form-control @error('supplier_id') is-invalid @enderror">
                        <option value="" disabled selected>{{ __('stockMovement.Select_supplier') }}</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->first_name }} {{ $supplier->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group" id="store-group">
                <div id="product-rows">
                    <div class="product-row row mb-2">
                        <div class="col-md-5">
                            <label for="from_store_id">{{ __('stockMovement.from') }}</label>
                            <select name="from_store_id" id="from_store_id" class="form-control @error('from_store_id') is-invalid @enderror">
                                <option value="">{{ __('stockMovement.Select_store') }}</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" {{ old('from_store_id') == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('from_store_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-5">
                            <label for="to_store_id">{{ __('stockMovement.to') }}</label>
                            <select name="to_store_id" id="to_store_id" class="form-control @error('to_store_id') is-invalid @enderror">
                                <option value="">{{ __('stockMovement.Select_store') }}</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" {{ old('to_store_id') == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('to_store_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- <button type="button" id="add-product-row" class="btn btn-sm btn-secondary mt-2">+ Add Another
                    Product
                </button> --}}
                </div>

                {{-- <div class="form-group">
                    <label for="products">{{ __('stockMovement.products') }}</label>
                    <select name="products[]" id="products" class="form-control @error('products') is-invalid @enderror">
                        <option value="products">{{ __('stockMovement.Select_product') }}</option>



                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ old('products') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('products')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div> --}}
                <div class="form-group">
                <label for="products">{{ __('stockMovement.products') }}</label>
                <div id="product-rows">
                    <div class="product-row row mb-2">
                        <div class="col-md-5">
                            <select name="products[]" id="products" class="form-control @error('products') is-invalid @enderror">
                                <option value="products">{{ __('stockMovement.Select_product') }}</option>



                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ old('products') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('products')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                            id="quantity" placeholder="Quantity" required min="1">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror"
                            id="cost_price" placeholder="Cost Price" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-row">&times;</button>
                        </div>
                    </div>
                </div>

                {{-- <button type="button" id="add-product-row" class="btn btn-sm btn-secondary mt-2">+ Add Another
                    Product
                </button> --}}
                </div>

                <div class="form-group">
                    <label for="reason">{{ __('stockMovement.reason') }}</label>
                    <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror"
                        id="reason" placeholder="{{ __('stockMovement.Enter_reason') }}" value="{{ old('reason') }}">
                    @error('reason')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <button class="btn btn-primary" type="submit">{{ __('common.Create') }}</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            let rowIndex = 1;

            $('#add-product-row').on('click', function () {
                const newRow = `
                <div class="product-row row mb-2">
                    <div class="col-md-5">
                        <select name="items[${rowIndex}][product_id]" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="items[${rowIndex}][quantity]" class="form-control" placeholder="Quantity" required min="1">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="items[${rowIndex}][cost_price]" class="form-control" placeholder="Cost Price" required step="0.01">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-row">&times;</button>
                    </div>
                </div>`;
                $('#product-rows').append(newRow);
                rowIndex++;
            });

            $(document).on('click', '.remove-row', function () {
                $(this).closest('.product-row').remove();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const movementTypeSelect = document.getElementById('movement_type');
        const supplierGroup = document.getElementById('supplier-group');
        const storeGroup = document.getElementById('store-group');

        function toggleSupplierField() {
            if (movementTypeSelect.value === 'adjustment') {
                supplierGroup.style.display = 'none';
            } else {
                supplierGroup.style.display = 'block';
            }
        }
        function toggleStoreField() {
            if (movementTypeSelect.value !== 'adjustment') {
                storeGroup.style.display = 'none';
            } else {
                storeGroup.style.display = 'block';
            }
        }

        // Initial check on page load
        toggleSupplierField();
        toggleStoreField();

        // Event listener for movement type change
        movementTypeSelect.addEventListener('change', toggleSupplierField);
        movementTypeSelect.addEventListener('change', toggleStoreField);
    });
</script>
@endsection
