<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; margin: 40px; }
        h1 { margin-bottom: 4px; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { border-bottom: 1px solid #ddd; padding: 10px 8px; text-align: left; }
        .totals { margin-top: 24px; width: 280px; margin-left: auto; }
        .totals td:last-child { text-align: right; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Print</button>
    <h1>MyBestStore.pk</h1>
    <p class="muted">Invoice</p>
    <p><strong>Order:</strong> {{ $order->order_number }}<br>
    <strong>Date:</strong> {{ $order->created_at?->format('M j, Y') }}</p>

    <p>
        <strong>Bill to</strong><br>
        {{ $order->customer_name }}<br>
        {{ $order->customer_email }}<br>
        @if ($order->customer_phone) {{ $order->customer_phone }} @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rs {{ number_format($item->unit_price) }}</td>
                    <td>Rs {{ number_format($item->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td>Rs {{ number_format($order->subtotal) }}</td></tr>
        <tr><td>Shipping</td><td>Rs {{ number_format($order->shipping) }}</td></tr>
        <tr><td><strong>Total</strong></td><td><strong>Rs {{ number_format($order->total) }}</strong></td></tr>
    </table>

    <p class="muted" style="margin-top:40px;">Payment status: {{ $order->payment_status ?? 'pending' }} · Order status: {{ $order->status }}</p>
</body>
</html>
