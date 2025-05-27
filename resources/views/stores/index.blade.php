@extends('layouts.admin')

@section('title', __('store.store_List'))
@section('content-header', __('store.store_List'))
@section('content-actions')
<a href="{{route('stores.create')}}" class="btn btn-primary">{{ __('store.Add_store') }}</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
<style>
    /* Hide the native checkbox */
    .custom-checkbox input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 18px;
    height: 18px;
    border: 2px solid #cbd5e0; /* light gray border */
    border-radius: 4px;
    position: relative;
    cursor: not-allowed;
    background-color: #f0f0f0;
    }
    /* Checked state */
    .custom-checkbox input[type="checkbox"]:checked {
    background-color: #4f46e5; /* Indigo color */
    border-color: #4f46e5;
    }
    /* Checked state: show checkmark */
    .custom-checkbox input[type="checkbox"]:checked::after {
    content: "";
    position: absolute;
    top: 2px;
    left: 6px;
    width: 4px;
    height: 9px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
    }
    /* Disabled styling */
    .custom-checkbox input[type="checkbox"]:disabled {
    background-color: #e2e8f0;
    border-color: #a0aec0;
    cursor: not-allowed;
    }
    .custom-checkbox input[type="checkbox"]:disabled:checked {
    background-color: #667eea; /* lighter indigo for disabled checked */
    border-color: #667eea;
    }
</style>
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
                <th class="text-center px-4 py-3" style="border-top-left-radius: 12px;">{{ __('store.ID') }}</th>
                <th class="text-start px-4 py-3">{{ __('store.Name') }}</th>
                <th class="text-center px-4 py-3">{{ __('store.is_admin') }}</th>
                <th class="text-center px-4 py-3">{{ __('common.Created_At') }}</th>
                <th class="text-center px-4 py-3" style="border-top-right-radius: 12px;">{{ __('store.Actions') }}</th>
            </tr>
        </thead>

        <!-- Table Body -->
        <tbody>
            @foreach ($stores as $store)
            <tr class="transition"
                style="border-bottom: 1px solid rgba(255, 255, 255, 0.2); transition: background 0.3s ease-in-out;">
                <td class="text-center fw-bold px-4 py-3 ">{{ $store->id }}</td>
                <td class="text-start fw-semibold px-4 py-3 ">{{ $store->name }}</td>
                <td class="text-center px-4 py-3 custom-checkbox">
                    <input type="checkbox" disabled {{ $store->is_admin ? 'checked' : '' }}>
                </td>
                <td class="text-center px-4 py-3 text-muted">{{ $store->created_at->format('Y-m-d') }}</td>
                <td class="text-center px-4 py-3">
                    <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px;">
                        <a href="{{ route('stores.edit', $store) }}"
                            class="btn btn-sm px-3 py-1 shadow-sm rounded-pill"
                            style="background: #2980b9; color: white; border: none;">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm px-3 py-1 shadow-sm rounded-pill btn-delete"
                            data-url="{{ route('stores.destroy', $store) }}"
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

        {{ $stores->render() }}
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
                title: '{{ __('store.Sure') }}', // Wrap in quotes
                text: '{{ __('store.Really_Delete') }}', // Wrap in quotes
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('store.Yes_Delete') }}', // Wrap in quotes
                cancelButtonText: '{{ __('store.No') }}', // Wrap in quotes
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
