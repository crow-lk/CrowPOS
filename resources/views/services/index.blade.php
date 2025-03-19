@extends('layouts.admin')

@section('title', __('service.Service_List'))
@section('content-header', __('service.Service_List'))
@section('content-actions')
<a href="{{route('services.create')}}" class="btn btn-primary">{{ __('service.Create_Service') }}</a>
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
                <th class="text-center px-4 py-3" style="border-top-left-radius: 12px;">{{ __('service.ID') }}</th>
                <th class="text-start px-4 py-3">{{ __('service.Name') }}</th>
                <th class="text-center px-4 py-3">{{ __('service.Created_At') }}</th>
                <th class="text-center px-4 py-3">{{ __('service.Updated_At') }}</th>
                <th class="text-center px-4 py-3" style="border-top-right-radius: 12px;">{{ __('service.Actions') }}</th>
            </tr>
        </thead>

        <!-- Table Body -->
        <tbody>
            @foreach ($services as $service)
            <tr class="transition"
                style="border-bottom: 1px solid rgba(255, 255, 255, 0.2); transition: background 0.3s ease-in-out;">
                <td class="text-center   px-4 py-3 ">{{ $service->id }}</td>
                <td class="text-start  px-4 py-3 ">{{ $service->name }}</td>
                <td class="text-center px-4 py-3 text-muted">{{ $service->created_at->format('Y-m-d') }}</td>
                <td class="text-center px-4 py-3 text-muted">{{ $service->updated_at->format('Y-m-d') }}</td>
                <td class="text-center px-4 py-3">
                    <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px;">
                        <a href="{{ route('services.edit', $service) }}"
                            class="btn btn-sm px-3 py-1 shadow-sm rounded-pill"
                            style="background: #2980b9; color: white; border: none;">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm px-3 py-1 shadow-sm rounded-pill btn-delete"
                            data-url="{{ route('services.destroy', $service) }}"
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

        {{ $services->render() }}
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
                title: '{{ __('service.sure') }}', // Wrap in quotes
                text: '{{ __('service.really_delete') }}', // Wrap in quotes
                icon: 'warning', // Fix the icon string
                showCancelButton: true,
                confirmButtonText: '{{ __('service.yes_delete') }}', // Wrap in quotes
                cancelButtonText: '{{ __('service.No') }}', // Wrap in quotes
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
