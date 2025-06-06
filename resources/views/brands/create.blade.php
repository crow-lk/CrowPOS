@extends('layouts.admin')

@section('title', __('brand.Create_Brand'))
@section('content-header', __('brand.Create_Brand'))

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('brand.Name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       id="name"
                       placeholder="{{ __('brand.Name') }}" value="{{ old('name') }}">
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            {{-- <div class="form-group">
                <label for="product_type_id">{{ __('brand.Product_Type') }}</label>
                <select name="product_type_id" id="product_type_id" class="form-control @error('product_type_id') is-invalid @enderror">
                    <option value="">{{ __('brand.Select_ProductType') }}</option>
                    @foreach($productTypes as $productType)
                        <option value="{{ $productType->id }}" {{ old('product_type_id') == $productType->id ? 'selected' : '' }}>
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

            <button class="btn btn-primary" type="submit">{{ __('common.Create') }}</button>
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
