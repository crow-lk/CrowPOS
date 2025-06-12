@extends('layouts.admin')

@section('title', __('store.Update'))
@section('content-header', __('store.Update'))

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('stores.update', $store) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">{{ __('store.Name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       id="name"
                       placeholder="{{ __('store.Name') }}" value="{{ old('name', $store->name) }}">
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            {{-- <div class="form-group">
                <label for="product_type_id">{{ __('store.Product_Type') }}</label>
                <select name="product_type_id" id="product_type_id" class="form-control @error('product_type_id') is-invalid @enderror">
                    <option value="">{{ __('store.Select_ProductType') }}</option>
                    @foreach($productTypes as $productType)
                        <option value="{{ $productType->id }}" {{ old('product_type_id', $store->product_type_id) == $productType->id ? 'selected' : '' }}>
                            {{ $productType->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_type_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div> --}}

            <button class="btn btn-primary" type="submit">{{ __('common.Update') }}</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(document).ready(function () {
        bsCustomFileInput.init();
    });
</script>
@endsection
