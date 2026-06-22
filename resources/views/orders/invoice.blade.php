<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} | MyBestStore</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">
</head>
<body class="order-invoice-page">
    <div class="order-invoice-toolbar no-print">
        <a href="{{ route('order.invoice.download', $order->order_number) }}" class="mbs-btn mbs-btn-primary">Download PDF</a>
        <button type="button" class="mbs-btn mbs-btn-outline order-invoice-print-btn" onclick="window.print()">Print Invoice</button>
        <a href="{{ route('order.success', $order->order_number) }}" class="mbs-btn mbs-btn-outline order-invoice-back-btn">Back to Order</a>
    </div>

    @if (session('error'))
        <div class="order-invoice-alert no-print" role="alert">{{ session('error') }}</div>
    @endif

    @include('orders._invoice-body', ['order' => $order])

    @if (request()->boolean('print'))
        <script>window.addEventListener('load', () => window.print());</script>
    @endif
</body>
</html>
