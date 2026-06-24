<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; font-family: DejaVu Sans, Arial, sans-serif; color: #0f172a; background: #ffffff; font-size: 12px; }
        .sheet { border: 1px solid #fed7aa; border-radius: 8px; padding: 24px; }
        .header { display: table; width: 100%; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #fed7aa; }
        .header-left, .header-right { display: table-cell; vertical-align: top; }
        .header-right { text-align: right; width: 45%; }
        .logo { max-width: 150px; height: auto; }
        .logo-text { margin: 0; font-size: 18px; font-weight: 700; color: #f97316; }
        .kicker { margin: 0 0 4px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: #f97316; }
        .title { margin: 0 0 6px; font-size: 18px; color: #0f172a; }
        .date { margin: 0 0 10px; color: #64748b; }
        .badges span { display: inline-block; margin-left: 6px; padding: 4px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; }
        .badge-payment { background: #ecfdf3; color: #15803d; }
        .badge-order { background: #fff7ed; color: #ea580c; }
        .invoice-scan { margin-top: 8px; text-align: right; }
        .invoice-scan-grid { display: inline-block; text-align: center; }
        .invoice-scan-qr { width: 70px; height: 70px; }
        .invoice-scan-barcode { max-width: 160px; height: 34px; display: block; margin: 4px auto 0; }
        .invoice-scan-code { margin: 4px 0 0; font-size: 9px; font-weight: 700; letter-spacing: 0.04em; color: #0f172a; }
        .info-grid { display: table; width: 100%; margin-bottom: 18px; }
        .info-card { display: table-cell; width: 33.33%; vertical-align: top; padding: 10px; border: 1px solid #fed7aa; background: #fffbf7; }
        .info-card h2 { margin: 0 0 8px; font-size: 11px; color: #f97316; text-transform: uppercase; letter-spacing: 0.04em; }
        .info-card p { margin: 0 0 4px; line-height: 1.45; }
        .info-card strong { color: #64748b; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 10px 8px; border-bottom: 1px solid #ffedd5; text-align: left; vertical-align: top; }
        th { background: #fff7ed; color: #0f172a; font-size: 11px; }
        .product-name { font-weight: 600; color: #0f172a; }
        .summary { width: 240px; margin-left: auto; margin-top: 14px; }
        .summary div { display: table; width: 100%; padding: 4px 0; }
        .summary span, .summary strong { display: table-cell; }
        .summary strong { text-align: right; }
        .grand { margin-top: 8px; padding-top: 8px; border-top: 1px solid #fed7aa; font-size: 14px; font-weight: 700; color: #f97316; }
        .footer { margin-top: 24px; padding-top: 14px; border-top: 1px solid #fed7aa; text-align: center; color: #64748b; font-size: 11px; }
    </style>
</head>
<body>
@php
    use App\Support\Mbs;
    use App\Support\OrderPresenter;
    use App\Services\BarcodeService;

    $logoDataUri = OrderPresenter::pdfImageDataUri('logo.png');
    $barcodeService = app(BarcodeService::class);
    $scanCode = $order->order_barcode ?: $order->order_number;
@endphp
    <div class="sheet">
        <div class="header">
            <div class="header-left">
                @if ($logoDataUri)
                    <img src="{{ $logoDataUri }}" alt="{{ Mbs::storeLabel() }}" class="logo">
                @else
                    <p class="logo-text">{{ Mbs::storeLabel() }}</p>
                @endif
            </div>
            <div class="header-right">
                <p class="kicker">INVOICE</p>
                <h1 class="title">Invoice No. {{ $order->order_number }}</h1>
                <p class="date">{{ $order->created_at?->format('M j, Y g:i A') }}</p>
                <div class="badges">
                    <span class="badge-payment">{{ OrderPresenter::paymentStatusLabel($order->payment_status) }}</span>
                    <span class="badge-order">{{ OrderPresenter::statusLabel($order->order_status) }}</span>
                </div>
                @if ($order->order_barcode)
                    <div class="invoice-scan">
                        <div class="invoice-scan-grid">
                            <img src="{{ $barcodeService->qrImageUrl($scanCode, 70) }}" alt="QR" class="invoice-scan-qr">
                            <div class="invoice-scan-barcode">{!! $barcodeService->barcodeSvg($scanCode, 34, 1) !!}</div>
                            <p class="invoice-scan-code">{{ $scanCode }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <h2>Customer Details</h2>
                <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
            </div>
            <div class="info-card">
                <h2>Shipping Address</h2>
                <p><strong>Address:</strong> {{ $order->shipping_address }}</p>
                <p><strong>City:</strong> {{ $order->city }}</p>
                <p><strong>Province:</strong> {{ $order->province }}</p>
                <p><strong>Country:</strong> {{ $order->country }}</p>
                @if ($order->postal_code)
                    <p><strong>Postal Code:</strong> {{ $order->postal_code }}</p>
                @endif
            </div>
            <div class="info-card">
                <h2>Payment Details</h2>
                <p><strong>Method:</strong> {{ OrderPresenter::paymentLabel($order->payment_method) }}</p>
                <p><strong>Payment Status:</strong> {{ OrderPresenter::paymentStatusLabel($order->payment_status) }}</p>
                <p><strong>Order Status:</strong> {{ OrderPresenter::statusLabel($order->order_status) }}</p>
                @if ($order->shipping_status)
                    <p><strong>Shipping Status:</strong> {{ OrderPresenter::shippingStatusLabel($order->shipping_status) }}</p>
                @endif
                @if ($order->courier_name)
                    <p><strong>Courier:</strong> {{ $order->courier_name }}</p>
                @endif
                @if ($order->tracking_number)
                    <p><strong>Tracking:</strong> {{ $order->tracking_number }}</p>
                @endif
                @if ($order->dispatched_at)
                    <p><strong>Dispatched:</strong> {{ $order->dispatched_at->format('M j, Y g:i A') }}</p>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td><span class="product-name">{{ $item->product_name }}</span></td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ Mbs::price((int) $item->unit_price) }}</td>
                        <td>{{ Mbs::price((int) $item->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div><span>Subtotal</span><strong>{{ Mbs::price((int) $order->subtotal) }}</strong></div>
            <div><span>Shipping</span><strong>{{ (int) $order->shipping_amount > 0 ? Mbs::price((int) $order->shipping_amount) : 'Free' }}</strong></div>
            <div><span>Discount</span><strong>{{ (int) $order->discount_amount > 0 ? '-'.Mbs::price((int) $order->discount_amount) : '—' }}</strong></div>
            <div class="grand"><span>Grand Total</span><strong>{{ Mbs::price((int) $order->total_amount) }}</strong></div>
        </div>

        <div class="footer">
            <p>Thank you for shopping with {{ Mbs::storeLabel() }}</p>
            <p>{{ OrderPresenter::supportMessage() }}</p>
        </div>
    </div>
</body>
</html>
