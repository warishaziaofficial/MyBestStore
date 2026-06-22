<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Invoice</title>
    <style>
        body { margin: 0; padding: 0; background: #f5faff; font-family: "Segoe UI", Arial, sans-serif; color: #082b4f; }
        .wrapper { max-width: 640px; margin: 0 auto; padding: 24px 16px; }
        .card { background: #ffffff; border: 1px solid #cfe3f8; border-radius: 12px; overflow: hidden; }
        .header { padding: 24px; text-align: center; border-bottom: 1px solid #cfe3f8; background: #ffffff; }
        .header img { max-width: 180px; height: auto; }
        .body { padding: 24px; }
        h1 { margin: 0 0 8px; font-size: 22px; color: #082b4f; }
        p { margin: 0 0 12px; line-height: 1.6; color: #60758c; font-size: 14px; }
        .meta { margin: 20px 0; padding: 16px; background: #eaf4ff; border: 1px solid #cfe3f8; border-radius: 8px; }
        .meta-row { display: flex; justify-content: space-between; gap: 12px; padding: 6px 0; font-size: 14px; }
        .meta-row strong { color: #082b4f; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; }
        th, td { padding: 10px 8px; border-bottom: 1px solid #eaf4ff; text-align: left; vertical-align: top; }
        th { color: #082b4f; font-size: 13px; }
        .product-cell { display: flex; gap: 10px; align-items: center; }
        .product-cell img { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #cfe3f8; }
        .totals { margin-top: 16px; padding-top: 8px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .totals-row--grand { margin-top: 8px; padding-top: 12px; border-top: 1px solid #cfe3f8; font-size: 16px; font-weight: 700; color: #005aa7; }
        .address { margin-top: 20px; padding: 16px; background: #f5faff; border-radius: 8px; border: 1px solid #cfe3f8; }
        .footer { padding: 20px 24px; text-align: center; background: #eaf4ff; border-top: 1px solid #cfe3f8; font-size: 13px; color: #60758c; }
    </style>
</head>
<body>
@php
    use App\Support\Mbs;
    use App\Support\OrderPresenter;
@endphp
<div class="wrapper">
    <div class="card">
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="MyBestStore.pk">
        </div>
        <div class="body">
            <h1>Thank you for your order!</h1>
            <p>Hi {{ $order->customer_name }}, your order has been placed successfully. Below are your invoice details.</p>

            <div class="meta">
                <div class="meta-row"><span>Order Number</span><strong>{{ $order->order_number }}</strong></div>
                <div class="meta-row"><span>Order Date</span><strong>{{ $order->created_at?->format('M j, Y g:i A') }}</strong></div>
                <div class="meta-row"><span>Payment Method</span><strong>{{ OrderPresenter::paymentLabel($order->payment_method) }}</strong></div>
                <div class="meta-row"><span>Payment Status</span><strong>{{ OrderPresenter::paymentStatusLabel($order->payment_status) }}</strong></div>
                <div class="meta-row"><span>Order Status</span><strong>{{ OrderPresenter::statusLabel($order->order_status) }}</strong></div>
                @if ($order->order_barcode)
                    <div class="meta-row"><span>Order Barcode</span><strong>{{ $order->order_barcode }}</strong></div>
                @endif
                @if ($order->shipping_status)
                    <div class="meta-row"><span>Shipping Status</span><strong>{{ OrderPresenter::shippingStatusLabel($order->shipping_status) }}</strong></div>
                @endif
                @if ($order->courier_name)
                    <div class="meta-row"><span>Courier</span><strong>{{ $order->courier_name }}</strong></div>
                @endif
                @if ($order->tracking_number)
                    <div class="meta-row"><span>Tracking Number</span><strong>{{ $order->tracking_number }}</strong></div>
                @endif
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                <div class="product-cell">
                                    <img src="{{ Mbs::image($item->product_image) }}" alt="{{ $item->product_name }}">
                                    <span>{{ $item->product_name }}</span>
                                </div>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ Mbs::price((int) $item->unit_price) }}</td>
                            <td>{{ Mbs::price((int) $item->line_total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div class="totals-row"><span>Subtotal</span><strong>{{ Mbs::price((int) $order->subtotal) }}</strong></div>
                <div class="totals-row"><span>Shipping</span><strong>{{ (int) $order->shipping_amount > 0 ? Mbs::price((int) $order->shipping_amount) : 'Free' }}</strong></div>
                <div class="totals-row"><span>Discount</span><strong>{{ (int) $order->discount_amount > 0 ? '-'.Mbs::price((int) $order->discount_amount) : '—' }}</strong></div>
                <div class="totals-row totals-row--grand"><span>Grand Total</span><strong>{{ Mbs::price((int) $order->total_amount) }}</strong></div>
            </div>

            <div class="address">
                <p style="margin-bottom:8px;color:#082b4f;font-weight:700;">Shipping Address</p>
                <p style="margin:0;">{{ OrderPresenter::shippingAddress($order) }}</p>
                <p style="margin-top:12px;">Email: {{ $order->customer_email }}<br>Phone: {{ $order->customer_phone }}</p>
            </div>

            <p style="margin-top:20px;">If you have any questions about your order, contact us at <a href="{{ route('contact') }}" style="color:#005aa7;">MyBestStore Support</a>.</p>
        </div>
        <div class="footer">
            Thank you for shopping with MyBestStore.pk
        </div>
    </div>
</div>
</body>
</html>
