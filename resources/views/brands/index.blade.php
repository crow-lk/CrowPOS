@extends('layouts.admin')

@section('title', __('brand.Brand_List'))
@section('content-header', __('brand.Brand_List'))
@section('content-actions')
<a href="{{route('brands.create')}}" class="btn btn-primary">{{ __('brand.Add_Brand') }}</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('brand.ID') }}</th>
                    <th>{{ __('brand.Name') }}</th>
                    <th>{{ __('brand.ProductType_Name') }}</th>
                    <th>{{ __('common.Created_At') }}</th>
                    <th>{{ __('brand.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $brand)
                <tr>
                    <td>{{$brand->id}}</td>
                    <td>{{$brand->name}}</td>
                    <td>{{$brand->productType->name}}</td>
                    <td>{{$brand->created_at}}</td>
                    <td>
                        <a href="{{ route('brands.edit', $brand) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-danger btn-delete" data-url="{{route('brands.destroy', $brand)}}"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $brands->render() }}
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
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: '{{ __('brand.Sure') }}', // Wrap in quotes
                text: '{{ __('brand.Really_Delete') }}', // Wrap in quotes
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('brand.Yes_Delete') }}', // Wrap in quotes
                cancelButtonText: '{{ __('brand.No') }}', // Wrap in quotes
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
