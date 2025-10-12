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
            <div id="products-rows">
                <div class="product-row row mb-2">
                    <div class="col-md-5">
                        <div class="position-relative">
                            <input type="text" class="form-control product-search-input @error('products') is-invalid @enderror" 
                                   placeholder="Type to search products..." 
                                   autocomplete="off">
                            <input type="hidden" name="products[]" class="product-id-input">
                            <div class="product-suggestions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ced4da; border-top: none; max-height: 200px; overflow-y: auto; z-index: 1000;">
                            </div>
                        </div>
                        @error('products.*')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="quantities[]" class="form-control @error('quantities.*') is-invalid @enderror"
                        placeholder="Quantity" required min="1">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="cost_prices[]" class="form-control @error('cost_prices.*') is-invalid @enderror"
                        placeholder="Cost Price">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger delete-product">Delete</button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" id="add-product">Add Another Product</button>
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
        $(document).ready(function() {
            // Product data for autocomplete
            const products = [
                @foreach ($productDetails as $productDetail)
                {
                    id: {{ $productDetail->id }},
                    name: "{{ addslashes($productDetail->name) }}"
                },
                @endforeach
            ];

            // Initialize autocomplete on existing product search inputs
            function initializeAutocomplete(container) {
                container.find('.product-search-input').each(function() {
                    const $input = $(this);
                    const $hiddenInput = $input.siblings('.product-id-input');
                    const $suggestions = $input.siblings('.product-suggestions');

                    if ($input.data('autocomplete-initialized')) return;
                    $input.data('autocomplete-initialized', true);

                    $input.on('input', function() {
                        const query = $(this).val().toLowerCase();
                        
                        if (query.length === 0) {
                            $suggestions.hide().empty();
                            $hiddenInput.val('');
                            return;
                        }

                        const matches = products.filter(product => 
                            product.name.toLowerCase().includes(query)
                        ).slice(0, 10); // Limit to 10 results

                        if (matches.length > 0) {
                            const suggestionsHtml = matches.map(product => 
                                `<div class="suggestion-item" data-id="${product.id}" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                                    ${product.name}
                                </div>`
                            ).join('');
                            
                            $suggestions.html(suggestionsHtml).show();
                        } else {
                            $suggestions.html('<div style="padding: 8px 12px; color: #666;">No products found</div>').show();
                        }
                    });

                    // Handle suggestion clicks
                    $suggestions.on('click', '.suggestion-item', function() {
                        const productId = $(this).data('id');
                        const productName = $(this).text();
                        
                        $input.val(productName);
                        $hiddenInput.val(productId);
                        $suggestions.hide();
                    });

                    // Hide suggestions when clicking outside
                    $(document).on('click', function(e) {
                        if (!$input.closest('.position-relative').is(e.target) && 
                            !$input.closest('.position-relative').has(e.target).length) {
                            $suggestions.hide();
                        }
                    });

                    // Handle keyboard navigation
                    $input.on('keydown', function(e) {
                        const $items = $suggestions.find('.suggestion-item');
                        const $current = $items.filter('.highlighted');
                        
                        if (e.key === 'ArrowDown') {
                            e.preventDefault();
                            if ($current.length === 0) {
                                $items.first().addClass('highlighted').css('background-color', '#f8f9fa');
                            } else {
                                $current.removeClass('highlighted').css('background-color', '');
                                const next = $current.next('.suggestion-item');
                                if (next.length) {
                                    next.addClass('highlighted').css('background-color', '#f8f9fa');
                                } else {
                                    $items.first().addClass('highlighted').css('background-color', '#f8f9fa');
                                }
                            }
                        } else if (e.key === 'ArrowUp') {
                            e.preventDefault();
                            if ($current.length === 0) {
                                $items.last().addClass('highlighted').css('background-color', '#f8f9fa');
                            } else {
                                $current.removeClass('highlighted').css('background-color', '');
                                const prev = $current.prev('.suggestion-item');
                                if (prev.length) {
                                    prev.addClass('highlighted').css('background-color', '#f8f9fa');
                                } else {
                                    $items.last().addClass('highlighted').css('background-color', '#f8f9fa');
                                }
                            }
                        } else if (e.key === 'Enter') {
                            e.preventDefault();
                            if ($current.length) {
                                $current.click();
                            }
                        } else if (e.key === 'Escape') {
                            $suggestions.hide();
                        }
                    });
                });
            }

            // Initialize autocomplete on page load
            initializeAutocomplete($('#products-rows'));

            // Handle adding new product rows
            $('#add-product').on('click', function() {
                const productRowsContainer = $('#products-rows');
                const newProductRow = productRowsContainer.find('.product-row:first').clone();
                
                // Clear values
                newProductRow.find('input').val('').removeClass('is-invalid');
                newProductRow.find('.product-suggestions').hide().empty();
                
                // Remove autocomplete initialization flag
                newProductRow.find('.product-search-input').removeData('autocomplete-initialized');
                
                // Add to container
                productRowsContainer.append(newProductRow);
                
                // Initialize autocomplete on new row
                initializeAutocomplete(newProductRow);
                
                // Wire up delete button for the new row
                newProductRow.find('.delete-product').off('click').on('click', function() {
                    newProductRow.remove();
                });
            });

            // Handle delete for existing rows
            $(document).on('click', '.delete-product', function() {
                const row = $(this).closest('.product-row');
                if ($('#products-rows .product-row').length > 1) {
                    row.remove();
                }
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
