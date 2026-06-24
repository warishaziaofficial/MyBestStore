@php
    use App\Support\Mbs;
    use App\Support\OrderPresenter;
@endphp

<div class="order-invoice-sheet">
    <header class="order-invoice-header">
        <div class="order-invoice-brand">
            <img src="{{ asset('logo.png') }}" alt="{{ Mbs::storeLabel() }}" class="order-invoice-logo">
        </div>
        <div class="order-invoice-header-meta">
            <p class="order-invoice-kicker">INVOICE</p>
            <h1 class="order-invoice-heading">Invoice No. {{ $order->order_number }}</h1>
            <p class="order-invoice-date">{{ $order->created_at?->format('M j, Y g:i A') }}</p>
            <div class="order-invoice-badges">
                <span class="order-invoice-badge order-invoice-badge--payment">{{ OrderPresenter::paymentStatusLabel($order->payment_status) }}</span>
                <span class="order-invoice-badge order-invoice-badge--order">{{ OrderPresenter::statusLabel($order->order_status) }}</span>
                @if ($order->shipping_status)
                    <span class="order-invoice-badge order-invoice-badge--shipping">{{ OrderPresenter::shippingStatusLabel($order->shipping_status) }}</span>
                @endif
            </div>
            @if ($order->order_barcode)
                <x-order-barcode :order="$order" variant="compact" class="order-invoice-barcode-compact" />
            @endif
        </div>
    </header>

    <div class="order-invoice-info-grid">
        <section class="order-invoice-info-card">
            <h2>Customer Details</h2>
            <dl>
                <div><dt>Name</dt><dd>{{ $order->customer_name }}</dd></div>
                <div><dt>Email</dt><dd>{{ $order->customer_email }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $order->customer_phone }}</dd></div>
            </dl>
        </section>
        <section class="order-invoice-info-card">
            <h2>Shipping Address</h2>
            <dl>
                <div><dt>Address</dt><dd>{{ $order->shipping_address }}</dd></div>
                <div><dt>City</dt><dd>{{ $order->city }}</dd></div>
                <div><dt>Province</dt><dd>{{ $order->province }}</dd></div>
                <div><dt>Country</dt><dd>{{ $order->country }}</dd></div>
                @if ($order->postal_code)
                    <div><dt>Postal Code</dt><dd>{{ $order->postal_code }}</dd></div>
                @endif
            </dl>
        </section>
        <section class="order-invoice-info-card">
            <h2>Payment Details</h2>
            <dl>
                <div><dt>Method</dt><dd>{{ OrderPresenter::paymentLabel($order->payment_method) }}</dd></div>
                <div><dt>Payment Status</dt><dd>{{ OrderPresenter::paymentStatusLabel($order->payment_status) }}</dd></div>
                <div><dt>Order Status</dt><dd>{{ OrderPresenter::statusLabel($order->order_status) }}</dd></div>
                @if ($order->shipping_status)
                    <div><dt>Shipping Status</dt><dd>{{ OrderPresenter::shippingStatusLabel($order->shipping_status) }}</dd></div>
                @endif
                @if ($order->courier_name)
                    <div><dt>Courier</dt><dd>{{ $order->courier_name }}</dd></div>
                @endif
                @if ($order->tracking_number)
                    <div>
                        <dt>Tracking Number</dt>
                        <dd>
                            @if ($trackingUrl = OrderPresenter::trackingUrl($order))
                                <a href="{{ $trackingUrl }}" target="_blank" rel="noopener">{{ $order->tracking_number }}</a>
                            @else
                                {{ $order->tracking_number }}
                            @endif
                        </dd>
                    </div>
                @endif
                @if ($order->dispatched_at)
                    <div><dt>Dispatched</dt><dd>{{ $order->dispatched_at->format('M j, Y g:i A') }}</dd></div>
                @endif
            </dl>
        </section>
    </div>

    <div class="order-invoice-table-wrap">
        <table class="order-invoice-table">
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
                        <td>
                            <div class="order-invoice-product">
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
    </div>

    <div class="order-invoice-summary">
        <div class="order-invoice-summary-row"><span>Subtotal</span><strong>{{ Mbs::price((int) $order->subtotal) }}</strong></div>
        <div class="order-invoice-summary-row"><span>Shipping</span><strong>{{ (int) $order->shipping_amount > 0 ? Mbs::price((int) $order->shipping_amount) : 'Free' }}</strong></div>
        <div class="order-invoice-summary-row"><span>Discount</span><strong>{{ (int) $order->discount_amount > 0 ? '-'.Mbs::price((int) $order->discount_amount) : '—' }}</strong></div>
        <div class="order-invoice-summary-row order-invoice-summary-grand"><span>Grand Total</span><strong>{{ Mbs::price((int) $order->total_amount) }}</strong></div>
    </div>

    <footer class="order-invoice-footer">
        <p>Thank you for shopping with {{ Mbs::storeLabel() }}</p>
        <p>{{ OrderPresenter::supportMessage() }}</p>
    </footer>
</div>
