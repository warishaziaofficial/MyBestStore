@extends('cms::layouts.admin')

@section('title', 'Reports & Analytics')

@section('page_heading')
<div>
    <h1 class="sf-page-title">Reports & Analytics</h1>
    <p class="sf-page-subtitle">Sales overview, inventory intelligence, and product performance insights.</p>
</div>
@endsection

@section('content')
@if (($stockAlerts['out_of_stock'] ?? 0) > 0)
    <div class="sf-alert sf-alert-warn">
        <strong>{{ $stockAlerts['out_of_stock'] }} product(s) out of stock.</strong>
        Admin email is sent automatically when a product reaches zero stock (not daily).
        Requires <code>MAIL_*</code> in <code>.env</code>.
    </div>
@elseif (($stockAlerts['low_stock'] ?? 0) > 0)
    <div class="sf-alert sf-alert-warn">
        <strong>{{ $stockAlerts['low_stock'] }} product(s) low on stock (≤ {{ $stockThreshold }}).</strong>
        Shown here only — no email is sent for low stock.
    </div>
@else
    <div class="sf-alert sf-alert-success">All products sufficiently stocked.</div>
@endif

<div class="sf-panel sf-ai-panel">
    <div class="sf-page-head" style="margin-bottom:12px;">
        <div>
            <h2 class="sf-report-heading" style="margin:0;">🤖 AI Insights</h2>
            <p class="cms-muted" style="margin:6px 0 0;">OpenAI analyzes your sales and inventory data for actionable recommendations.</p>
        </div>
        @if ($canEdit ?? true)
            <form method="POST" action="{{ route('cms.reports.ai') }}">
                @csrf
                <button type="submit" class="sf-btn sf-btn-primary" {{ $aiConfigured ? '' : 'disabled' }}>Generate AI Report</button>
            </form>
        @endif
    </div>
    @if ($aiError ?? null)
        <div class="sf-alert sf-alert-warn">{{ $aiError }}</div>
    @elseif (! ($aiConfigured ?? false))
        <p class="cms-muted">Add <code>OPENAI_API_KEY=sk-...</code> to your <code>.env</code> file to enable AI insights.</p>
    @elseif ($aiInsights ?? null)
        <div class="sf-ai-output">{!! nl2br(e($aiInsights)) !!}</div>
    @else
        <p class="cms-muted">Click <strong>Generate AI Report</strong> to get a smart summary of inventory, sales trends and next steps.</p>
    @endif
</div>

<div class="sf-kpi-row sf-kpi-row--6">
    <div class="sf-kpi-card">
        <div class="sf-kpi-icon sf-kpi-icon--green">📈</div>
        <div>
            <p class="sf-kpi-label">Total Revenue</p>
            <p class="sf-kpi-value">Rs {{ number_format($stats['total_revenue'] ?? 0) }}</p>
        </div>
    </div>
    <div class="sf-kpi-card">
        <div class="sf-kpi-icon sf-kpi-icon--blue">🛒</div>
        <div>
            <p class="sf-kpi-label">Total Orders</p>
            <p class="sf-kpi-value">{{ number_format($stats['total_orders'] ?? 0) }}</p>
        </div>
    </div>
    <div class="sf-kpi-card">
        <div class="sf-kpi-icon sf-kpi-icon--pink">↩</div>
        <div>
            <p class="sf-kpi-label">Refunds Processed</p>
            <p class="sf-kpi-value">Rs {{ number_format($refundTotals['amount'] ?? 0) }}</p>
        </div>
    </div>
    <div class="sf-kpi-card">
        <div class="sf-kpi-icon sf-kpi-icon--orange">⏳</div>
        <div>
            <p class="sf-kpi-label">Pending Orders</p>
            <p class="sf-kpi-value">{{ number_format($stats['pending_orders'] ?? 0) }}</p>
        </div>
    </div>
    <div class="sf-kpi-card">
        <div class="sf-kpi-icon sf-kpi-icon--orange">⚠</div>
        <div>
            <p class="sf-kpi-label">Low Stock (≤ {{ $stockThreshold }})</p>
            <p class="sf-kpi-value">{{ number_format($stockAlerts['low_stock'] ?? 0) }}</p>
        </div>
    </div>
    <div class="sf-kpi-card">
        <div class="sf-kpi-icon sf-kpi-icon--pink">✕</div>
        <div>
            <p class="sf-kpi-label">Out of Stock</p>
            <p class="sf-kpi-value">{{ number_format($stockAlerts['out_of_stock'] ?? 0) }}</p>
        </div>
    </div>
</div>

<div class="cms-detail-grid">
    <div class="sf-panel">
        <h2 style="margin:0 0 16px;font-size:1.05rem;font-weight:700;">Orders by Status</h2>
        <ul class="sf-list-stats">
            @forelse ($stats['orders_by_status'] ?? [] as $status => $count)
                <li><span>{{ ucfirst($status) }}</span><strong>{{ $count }}</strong></li>
            @empty
                <li class="cms-muted">No order data yet.</li>
            @endforelse
        </ul>
    </div>
    <div class="sf-panel">
        <h2 style="margin:0 0 16px;font-size:1.05rem;font-weight:700;">Orders by Source</h2>
        <ul class="sf-list-stats">
            @forelse ($stats['orders_by_source'] ?? [] as $source => $count)
                <li><span>{{ ucfirst($source) }}</span><strong>{{ $count }}</strong></li>
            @empty
                <li class="cms-muted">No source data yet.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="cms-detail-grid">
    <div class="sf-panel">
        <h2 style="margin:0 0 16px;font-size:1.05rem;font-weight:700;">Monthly Revenue</h2>
        <div class="sf-table-wrap">
            <table class="sf-table">
                <thead><tr><th>Month</th><th>Orders</th><th>Revenue</th></tr></thead>
                <tbody>
                    @forelse ($monthlyRevenue as $row)
                        <tr>
                            <td>{{ $row->month }}</td>
                            <td>{{ $row->orders }}</td>
                            <td>Rs {{ number_format($row->revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="sf-empty">No revenue data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="sf-panel">
        <h2 style="margin:0 0 16px;font-size:1.05rem;font-weight:700;">Top Products (All Time)</h2>
        <div class="sf-table-wrap">
            <table class="sf-table">
                <thead><tr><th>Product</th><th>Units</th><th>Revenue</th></tr></thead>
                <tbody>
                    @forelse ($topProducts as $row)
                        <tr>
                            <td>{{ $row->product_name }}</td>
                            <td>{{ $row->units }}</td>
                            <td>Rs {{ number_format($row->revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="sf-empty">No sales data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="sf-report-section" id="inventory-alerts">
    <h2 class="sf-report-heading">Inventory Alerts</h2>
    <div class="cms-detail-grid">
        <div class="sf-panel">
            <h3 style="margin:0 0 12px;font-size:1rem;font-weight:700;">Low Stock Alert <span class="sf-pill sf-pill--gray">≤ {{ $stockThreshold }} units</span></h3>
            <div class="sf-table-wrap">
                <table class="sf-table">
                    <thead><tr><th>Product</th><th>Stock</th><th>Price</th></tr></thead>
                    <tbody>
                        @forelse ($lowStockProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td><span class="sf-pill sf-pill--gray">{{ $product->stock }}</span></td>
                                <td>Rs {{ number_format($product->price) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="sf-empty">All products sufficiently stocked.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="sf-panel">
            <h3 style="margin:0 0 12px;font-size:1rem;font-weight:700;">Out of Stock Alert</h3>
            <div class="sf-table-wrap">
                <table class="sf-table">
                    <thead><tr><th>Product</th><th>Stock</th><th>Price</th></tr></thead>
                    <tbody>
                        @forelse ($outOfStockProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td><span class="sf-pill sf-pill--gray">0</span></td>
                                <td>Rs {{ number_format($product->price) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="sf-empty">No out-of-stock products.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="sf-report-section">
    <h2 class="sf-report-heading">Product Performance</h2>
    <div class="cms-detail-grid">
        <div class="sf-panel">
            <h3 style="margin:0 0 12px;font-size:1rem;font-weight:700;">Fast Moving Products <span class="cms-muted">Last 30 days</span></h3>
            <div class="sf-table-wrap">
                <table class="sf-table">
                    <thead><tr><th>Product</th><th>Units</th><th>Revenue</th></tr></thead>
                    <tbody>
                        @forelse ($fastMovingProducts as $row)
                            <tr>
                                <td>{{ $row->product_name }}</td>
                                <td>{{ $row->units }}</td>
                                <td>Rs {{ number_format($row->revenue) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="sf-empty">No recent sales data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="sf-panel">
            <h3 style="margin:0 0 12px;font-size:1rem;font-weight:700;">Slow Moving Products <span class="cms-muted">No sales in 90 days</span></h3>
            <div class="sf-table-wrap">
                <table class="sf-table">
                    <thead><tr><th>Product</th><th>Stock</th><th>Units Sold</th></tr></thead>
                    <tbody>
                        @forelse ($slowMovingProducts as $row)
                            <tr>
                                <td>{{ $row['product_name'] }}</td>
                                <td>{{ $row['stock'] }}</td>
                                <td>{{ $row['units_sold'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="sf-empty">No slow-moving products detected.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="sf-panel">
        <h3 style="margin:0 0 12px;font-size:1rem;font-weight:700;">Trending Products <span class="cms-muted">Last 7 days vs prior 7 days</span></h3>
        <div class="sf-table-wrap">
            <table class="sf-table">
                <thead><tr><th>Product</th><th>Recent</th><th>Prior</th><th>Growth</th></tr></thead>
                <tbody>
                    @forelse ($trendingProducts as $row)
                        <tr>
                            <td>{{ $row['product_name'] }}</td>
                            <td>{{ $row['recent_units'] }}</td>
                            <td>{{ $row['prior_units'] }}</td>
                            <td>
                                <span class="sf-pill sf-pill--{{ $row['growth_percent'] >= 0 ? 'green' : 'gray' }}">
                                    {{ $row['growth_percent'] >= 0 ? '+' : '' }}{{ $row['growth_percent'] }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="sf-empty">Not enough order history for trending analysis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="sf-panel">
        <h3 style="margin:0 0 12px;font-size:1rem;font-weight:700;">High Margin Products</h3>
        @if (! $hasCostPrice)
            <p class="cms-muted">Import <code>cms/Products-cost.sql</code> and set cost prices on products to enable this report.</p>
        @else
            <div class="sf-table-wrap">
                <table class="sf-table">
                    <thead><tr><th>Product</th><th>Price</th><th>Cost</th><th>Margin</th><th>Margin %</th></tr></thead>
                    <tbody>
                        @forelse ($highMarginProducts as $row)
                            <tr>
                                <td>{{ $row['product_name'] }}</td>
                                <td>Rs {{ number_format($row['price']) }}</td>
                                <td>Rs {{ number_format($row['cost_price']) }}</td>
                                <td>Rs {{ number_format($row['margin_amount']) }}</td>
                                <td><span class="sf-pill sf-pill--green">{{ $row['margin_percent'] }}%</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="sf-empty">Add cost prices on products to see margin rankings.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="sf-report-section">
    <h2 class="sf-report-heading">Demand &amp; Purchasing</h2>
    <div class="cms-detail-grid">
        <div class="sf-panel">
            <h3 style="margin:0 0 12px;font-size:1rem;font-weight:700;">Next Month Demand Forecast</h3>
            <p class="cms-muted" style="margin:0 0 12px;">Based on average monthly sales (last 3 months) with 5% growth factor.</p>
            <div class="sf-table-wrap">
                <table class="sf-table">
                    <thead><tr><th>Product</th><th>Stock</th><th>Avg / Month</th><th>Forecast</th></tr></thead>
                    <tbody>
                        @forelse ($demandForecast as $row)
                            <tr>
                                <td>{{ $row['product_name'] }}</td>
                                <td>{{ $row['current_stock'] }}</td>
                                <td>{{ $row['avg_monthly_units'] }}</td>
                                <td><strong>{{ $row['forecast_next_month'] }}</strong></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="sf-empty">Need order history to generate forecasts.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="sf-panel">
            <h3 style="margin:0 0 12px;font-size:1rem;font-weight:700;">Purchase Recommendation</h3>
            <p class="cms-muted" style="margin:0 0 12px;">Suggested reorder quantity when forecast exceeds current stock.</p>
            <div class="sf-table-wrap">
                <table class="sf-table">
                    <thead><tr><th>Product</th><th>Stock</th><th>Forecast</th><th>Order Qty</th><th>Priority</th></tr></thead>
                    <tbody>
                        @forelse ($purchaseRecommendations as $row)
                            <tr>
                                <td>{{ $row['product_name'] }}</td>
                                <td>{{ $row['current_stock'] }}</td>
                                <td>{{ $row['forecast_next_month'] }}</td>
                                <td><strong>{{ $row['recommended_qty'] }}</strong></td>
                                <td><span class="sf-pill sf-pill--gray">{{ ucfirst($row['priority']) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="sf-empty">No purchase recommendations right now.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
