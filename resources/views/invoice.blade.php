<!-- resources/views/invoice.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        .line {
            border-top: 1px solid #000;
            margin: 20px 0;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items th, .items td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .total {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>Crow.lk</h1>
    <h2>INVOICE</h2>
    <div class="line"></div>

    <div class="invoice-details">
        <p>Customer Name: {{ $orderData->customer_id }}</p>
        <p>Date: {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="line"></div>

    <h3>Items</h3>
    <table class="items">
        <thead>
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th>Price (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orderData->cart as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->pivot->quantity }}</td>
                    <td>{{ $item->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>

    <p class="total">Total Amount: {{ $orderData->amount }}</p>

    @php
        $amountPaid = (float) $orderData->amount;
        $totalAmount = $orderData->cart->sum(function($item) {
            return $item->price * $item->pivot->quantity;
        });
    @endphp

    @if ($amountPaid < $totalAmount)
        <p class="total">Amount to Pay: {{ number_format($totalAmount - $amountPaid, 2) }}</p>
    @endif

</body>
</html>
