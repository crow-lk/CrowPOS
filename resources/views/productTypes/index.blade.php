@extends('layouts.admin')

@section('title', __('productType.ProductType_List'))
@section('content-header', __('productType.ProductType_List'))
@section('content-actions')
<a href="{{route('productTypes.create')}}" class="btn btn-primary">{{ __('productType.Add_ProductType') }}</a>
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
                    <th>{{ __('productType.ID') }}</th>
                    <th>{{ __('productType.Name') }}</th>
                    <th>{{ __('productType.Category_Name') }}</th>
                    <th>{{ __('common.Created_At') }}</th>
                    <th>{{ __('productType.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productTypes as $productType)
                <tr>
                    <td>{{$productType->id}}</td>
                    <td>{{$productType->name}}</td>
                    <td>{{$productType->category->name}}</td>
                    <td>{{$productType->created_at}}</td>
                    <td>
                        <a href="{{ route('productTypes.edit', $productType) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-danger btn-delete" data-url="{{route('productTypes.destroy', $productType)}}"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $productTypes->render() }}
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
                title: '{{ __('productType.Sure') }}', // Wrap in quotes
                text: '{{ __('productType.Really_Delete') }}', // Wrap in quotes
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('productType.Yes_Delete') }}', // Wrap in quotes
                cancelButtonText: '{{ __('productType.No') }}', // Wrap in quotes
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
