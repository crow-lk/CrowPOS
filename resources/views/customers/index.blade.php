@extends('layouts.admin')

@section('title', __('customer.Customer_List'))
@section('content-header', __('customer.Customer_List'))
@section('content-actions')
<a href="{{route('customers.create')}}" class="btn btn-primary">{{ __('customer.Add_Customer') }}</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card">
    <div class="card-body">
    <div class="table-responsive">
    <table class="table table-hover align-middle shadow-lg rounded"
        style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; overflow: hidden; width: 100%;">

        <!-- Table Head -->
        <thead style="background: #2C3E50; color: white;">
            <tr>
                <th class="text-center px-4 py-3" style="border-top-left-radius: 12px;">#</th>
                <th class="text-center px-4 py-3">{{ __('customer.Avatar') }}</th>
                <th class="text-start px-4 py-3">{{ __('customer.First_Name') }}</th>
                <th class="text-start px-4 py-3">{{ __('customer.Last_Name') }}</th>
                <th class="text-start px-4 py-3">{{ __('customer.Email') }}</th>
                <th class="text-start px-4 py-3">{{ __('customer.Phone') }}</th>
                <th class="text-start px-4 py-3">{{ __('customer.Address') }}</th>
                <th class="text-center px-4 py-3">{{ __('common.Created_At') }}</th>
                <th class="text-center px-4 py-3" style="border-top-right-radius: 12px;">{{ __('customer.Actions') }}</th>
            </tr>
        </thead>

        <!-- Table Body -->
        <tbody>
            @foreach ($customers as $customer)
            <tr class="transition"
                style="border-bottom: 1px solid rgba(255, 255, 255, 0.2); transition: background 0.3s ease-in-out;">
                <td class="text-center fw-bold px-4 py-3 ">{{ $customer->id }}</td>
                <td class="text-center px-4 py-3">
                    <img width="50" src="{{ $customer->getAvatarUrl() }}" alt="Avatar" class="rounded-circle shadow">
                </td>
                <td class="text-start fw-semibold px-4 py-3 ">{{ $customer->first_name }}</td>
                <td class="text-start fw-semibold px-4 py-3 ">{{ $customer->last_name }}</td>
                <td class="text-start px-4 py-3 ">{{ $customer->email }}</td>
                <td class="text-start px-4 py-3 ">{{ $customer->phone }}</td>
                <td class="text-start px-4 py-3 ">{{ $customer->address }}</td>
                <td class="text-center px-4 py-3 text-muted">{{ $customer->created_at->format('Y-m-d') }}</td>
                <td class="text-center px-4 py-3">
                    <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px;">
                        <a href="{{ route('customers.edit', $customer) }}"
                            class="btn btn-sm px-3 py-1 shadow-sm rounded-pill"
                            style="background: #2980b9; color: white; border: none;">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm px-3 py-1 shadow-sm rounded-pill btn-delete"
                            data-url="{{ route('customers.destroy', $customer) }}"
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

        {{ $customers->render() }}
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
            })

            swalWithBootstrapButtons.fire({
                title: '{{ __('customer.sure') }}', // Wrap in quotes
                text: '{{ __('customer.really_delete') }}', // Wrap in quotes
                icon: 'warning', // Fix the icon string
                showCancelButton: true,
                confirmButtonText: '{{ __('customer.yes_delete') }}', // Wrap in quotes
                cancelButtonText: '{{ __('customer.No') }}', // Wrap in quotes
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
