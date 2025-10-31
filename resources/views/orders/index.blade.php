@extends('layouts.admin')

@section('title', __('order.Orders_List'))
@section('content-header', __('order.Orders_List'))
@section('content-actions')
<a href="{{route('cart.index')}}" class="btn btn-primary">{{ __('cart.title') }}</a>
@endsection
@section('content')
<div class="card">
    <div class="card-body">
    <div class="row">
    <div class="col-md-12">
        <form action="{{route('orders.index')}}">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="date" name="start_date" class="form-control" value="{{request('start_date')}}" />
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                </div>
                <div class="col-md-4">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="{{ __('order.Search_Placeholder') }}"
                           value="{{ request('search') }}" />
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100" type="submit">{{ __('order.submit') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

        <div class="table-responsive">
    <table class="table table-hover align-middle shadow-lg rounded"
        style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; overflow: hidden; width: 100%; margin-top:3px;">

        <!-- Table Head with Updated Styles -->
        <thead style="background: #2C3E50; color: white;">
            <tr>
                <th class="text-center px-4 py-3" style="border-top-left-radius: 12px;">{{ __('order.ID') }}</th>
                <th class="text-start px-4 py-3">{{ __('order.Customer_Name') }}</th>
                <th class="text-center px-4 py-3">{{ __('order.Total') }}</th>
                <th class="text-center px-4 py-3">{{ __('order.Discount') }}</th>
                <th class="text-center px-4 py-3">{{ __('order.Received_Amount') }}</th>
                <th class="text-center px-4 py-3">{{ __('order.Status') }}</th>
                <th class="text-center px-4 py-3">{{ __('order.To_Pay') }}</th>
                <th class="text-center px-4 py-3">{{ __('order.Created_At') }}</th>
                <th class="text-center px-4 py-3" style="border-top-right-radius: 12px;">{{ __('order.Actions') }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($orders as $order)
            <tr class="transition" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2); transition: background 0.3s ease-in-out;">
                <td class="text-center fw-bold px-4 py-3 ">{{$order->id}}</td>
                <td class="text-start fw-semibold px-4 py-3 ">{{$order->getCustomerName()}}</td>
                <td class="text-center px-4 py-3 ">{{ config('settings.currency_symbol') }} {{$order->formattedTotalAmount()}}</td>
                <td class="text-center px-4 py-3 ">{{ config('settings.currency_symbol') }} {{$order->formattedDiscount()}}</td>
                <td class="text-center px-4 py-3 ">{{ config('settings.currency_symbol') }} {{$order->formattedReceivedAmount()}}</td>
                <td class="text-center px-4 py-3">
                    @if($order->receivedAmount() == 0)
                        <span class="badge badge-danger">{{ __('order.Not_Paid') }}</span>
                    @elseif($order->receivedAmount() < $order->total())
                        <span class="badge badge-warning">{{ __('order.Partial') }}</span>
                    @elseif($order->receivedAmount() == $order->total())
                        <span class="badge badge-success">{{ __('order.Paid') }}</span>
                    @elseif($order->receivedAmount() > $order->total())
                        <span class="badge badge-info">{{ __('order.Change') }}</span>
                    @endif
                </td>
                <td class="text-center px-4 py-3">{{config('settings.currency_symbol')}} {{number_format($order->total() - $order->receivedAmount(), 2)}}</td>
                <td class="text-center px-4 py-3 text-muted">{{$order->created_at}}</td>
                <td class="text-center px-4 py-3">
                    @php
                        $outstandingAmount = max($order->total() - $order->receivedAmount(), 0);
                    @endphp
                    @if($outstandingAmount > 0)
                    <button class="btn btn-sm btn-outline-success mb-1 btnPartialPayment" data-toggle="modal"
                        data-target="#partialPaymentModal"
                        data-order-id="{{ $order->id }}"
                        data-remaining-amount="{{ number_format($outstandingAmount, 2, '.', '') }}">
                        <i class="fas fa-wallet"></i> {{ __('order.Receive_Payment') }}
                    </button>
                    @endif
                    <button class="btn btn-sm btn-secondary btnShowInvoice" data-toggle="modal"
                        data-target="#modalInvoice"
                        data-order-id="{{ $order->id }}"
                        data-customer-name="{{ $order->getCustomerName() }}"
                        data-total="{{ $order->total() }}"
                        data-sub-total="{{ $order->totalAmount() }}"
                        data-discount="{{ $order->discount() }}"
                        data-received="{{ $order->receivedAmount() }}"
                        data-items="{{ json_encode($order->items) }}"
                        data-created-at="{{ $order->created_at }}"
                        data-payment="{{ isset($order->payments) && count($order->payments) > 0 ? $order->payments[0]->amount : 0 }}">
                        <ion-icon size="small" name="eye"></ion-icon>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

        {{ $orders->render() }}
    </div>
</div>
@endsection
@section('model')
<!-- Modal -->
<div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInvoiceLabel">Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Placeholder for dynamic content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="partialPaymentModal" tabindex="-1" role="dialog" aria-labelledby="partialPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('orders.partial-payment') }}" id="partialPaymentForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="partialPaymentModalLabel">{{ __('order.Receive_Payment') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="order_id" id="modalOrderId" value="{{ old('order_id') }}">
                <div class="alert alert-warning" role="alert">
                    <strong>{{ __('order.To_Pay') }}:</strong>
                    {{ config('settings.currency_symbol') }}
                    <span id="remainingAmountLabel">0.00</span>
                </div>
                <div class="form-group">
                    <label for="partialAmount">{{ __('order.Received_Amount') }}</label>
                    <input type="number"
                           class="form-control"
                           id="partialAmount"
                           name="amount"
                           step="0.01"
                           min="0.01"
                           value="{{ old('amount') }}"
                           required>
                    @error('amount')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                    @error('order_id')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.Close') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('common.Submit') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Use event delegation to bind to the document for dynamically generated elements
    $(document).on('click', '.btnShowInvoice', function(event) {
        console.log("Modal show event triggered!");

        // Fetch data from the clicked button
        var button = $(this); // Button that triggered the modal
        var orderId = button.data('order-id');
        var customerName = button.data('customer-name');
        var totalAmount = button.data('total');
        var discount = button.data('discount');
        var subTotal = button.data('sub-total');
        var receivedAmount = button.data('received');
        var payment = button.data('payment');
        var createdAt = button.data('created-at');
        var items = button.data('items'); // Ensure this is correctly passed as a JSON

        // Log the data to ensure it's being captured correctly
        console.log({
            orderId,
            customerName,
            totalAmount,
            discount,
            subTotal,
            receivedAmount,
            createdAt,
            items
        });

        // Open the modal
        $('#modalInvoice').modal('show');

        // Populate the modal body with dynamic data (you can extend this part)
        var modalBody = $('#modalInvoice').find('.modal-body');

        // Construct items HTML if items exist
        var itemsHTML = '';
        if (items) {
            items.forEach(function(item, index) {
                itemsHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.product.product_detail.name}</td>
                <td>${item.product.product_detail.description || 'N/A'}</td>
                <td class="text-right">${parseFloat(item.product.price).toFixed(2)}</td>
                <td>${item.quantity}</td>
                <td class="text-right">${(parseFloat(item.price) * item.quantity).toFixed(2)}</td>
                <td class="text-right">{{config('settings.currency_symbol')}} ${parseFloat(item.customer_pay_amount || 0).toFixed(2)}</td>
                <td class="text-right ${parseFloat(item.balance_amount || 0) >= 0 ? 'text-success' : 'text-danger'}">{{config('settings.currency_symbol')}} ${parseFloat(item.balance_amount || 0).toFixed(2)}</td>
            </tr>
        `;
            });
        }

        // Update the modal body content
        modalBody.html(`
    <div class="card">
        <div class="card-header">
            Invoice <strong>${createdAt.split('T')[0]}</strong>
            <span class="float-right"> <strong>Status:</strong> ${

                        receivedAmount == 0?
                            '<span class="badge badge-danger">{{ __('order.Not_Paid') }}</span>':
                        receivedAmount < totalAmount ?
                            '<span class="badge badge-warning">{{ __('order.Partial') }}</span>':
                        receivedAmount == totalAmount?
                            '<span class="badge badge-success">{{ __('order.Paid') }}</span>':
                        receivedAmount > totalAmount?
                            '<span class="badge badge-info">{{ __('order.Change') }}</span>':''
            }</span>


        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-6">
                    <h6 class="mb-3">To: <strong>${customerName || 'N/A'}</strong></h6>
                </div>
            </div>
            <div class="table-responsive-sm">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Description</th>
                            <th class="text-right">Unit Cost</th>
                            <th>Qty</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Customer Pay</th>
                            <th class="text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHTML}
                    </tbody>
                    <tfoot>
                      <tr>
                        <th class="text-right" colspan="6">
                          Sub-Total:
                        </th>
                        <th class="right text-right" colspan="2">
                          <strong>{{config('settings.currency_symbol')}} ${parseFloat(subTotal).toFixed(2)}</strong>
                        </th>
                      </tr>
                      <tr>
                        <th class="text-right" colspan="6">
                          Discounted Amount:
                        </th>
                        <th class="right text-right" colspan="2">
                          <strong>{{config('settings.currency_symbol')}} ${parseFloat(discount).toFixed(2)}</strong>
                        </th>
                      </tr>
                      <tr>
                        <th class="text-right" colspan="6">
                          Total:
                        </th>
                        <th class="right text-right" colspan="2">
                          <strong>{{config('settings.currency_symbol')}} ${parseFloat(totalAmount).toFixed(2)}</strong>
                        </th>
                      </tr>

                      <tr>
                        <th class="text-right" colspan="6">
                          Paid:
                        </th>
                        <th class="right text-right" colspan="2">
                          <strong>{{config('settings.currency_symbol')}} ${parseFloat(receivedAmount).toFixed(2)}</strong>
                        </th>
                      </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
  </div>
</div>
`);
    });
    $(document).ready(function() {
        $('#partialPaymentModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var orderId = button.data('order-id');
            var remainingAmount = parseFloat(button.data('remaining-amount')) || 0;

            var modal = $(this);
            modal.find('#modalOrderId').val(orderId);
            modal.find('#remainingAmountLabel').text(remainingAmount.toFixed(2));
            modal.find('#partialAmount')
                .attr('max', remainingAmount.toFixed(2))
                .val(remainingAmount > 0 ? remainingAmount.toFixed(2) : '');
        });

        @if ($errors->has('amount') || $errors->has('order_id'))
            var previousOrderId = @json(old('order_id'));
            var previousAmount = @json(old('amount'));

            if (previousOrderId) {
                var triggerButton = $('[data-target="#partialPaymentModal"][data-order-id="' + previousOrderId + '"]').first();
                if (triggerButton.length) {
                    var outstanding = parseFloat(triggerButton.data('remaining-amount')) || 0;
                    $('#modalOrderId').val(previousOrderId);
                    $('#remainingAmountLabel').text(outstanding.toFixed(2));
                    $('#partialAmount')
                        .attr('max', outstanding.toFixed(2))
                        .val(previousAmount ? parseFloat(previousAmount).toFixed(2) : (outstanding > 0 ? outstanding.toFixed(2) : ''));
                }
            }

            $('#partialPaymentModal').modal('show');
        @endif
    });

</script>
@endsection
