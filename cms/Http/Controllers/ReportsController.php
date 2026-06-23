<?php

namespace Cms\Http\Controllers;

use Cms\Models\Order;
use Cms\Models\OrderItem;
use Cms\Models\Refund;
use Cms\Support\AdminStats;
use Cms\Support\AiReportInsights;
use Cms\Support\CmsAuth;
use Cms\Support\ReportAnalytics;
use Cms\Support\StockAlertNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(): View
    {
        $stats = AdminStats::dashboard();
        $lowStock = ReportAnalytics::lowStock();
        $outOfStock = ReportAnalytics::outOfStock();
        $fastMoving = ReportAnalytics::fastMoving();
        $purchaseRecommendations = ReportAnalytics::purchaseRecommendations();
        $highMargin = ReportAnalytics::highMargin();

        $topProducts = [];
        $monthlyRevenue = [];
        $refundTotals = ['count' => 0, 'amount' => 0];

        if (Schema::hasTable('OrderItems')) {
            $topProducts = OrderItem::query()
                ->select('product_name', DB::raw('SUM(quantity) as units'), DB::raw('SUM(line_total) as revenue'))
                ->groupBy('product_name')
                ->orderByDesc('revenue')
                ->limit(10)
                ->get()
                ->all();
        }

        if (Schema::hasTable('Orders')) {
            $monthlyRevenue = Order::query()
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as revenue, COUNT(*) as orders")
                ->where('status', '!=', 'cancelled')
                ->groupBy('month')
                ->orderByDesc('month')
                ->limit(6)
                ->get()
                ->all();
        }

        if (Schema::hasTable('Refunds')) {
            $refundTotals = [
                'count' => Refund::query()->count(),
                'amount' => (int) Refund::query()->whereIn('status', ['approved', 'completed'])->sum('amount'),
            ];
        }

        return view('cms::reports.index', [
            'stats' => $stats,
            'topProducts' => $topProducts,
            'monthlyRevenue' => $monthlyRevenue,
            'refundTotals' => $refundTotals,
            'lowStockProducts' => $lowStock,
            'outOfStockProducts' => $outOfStock,
            'fastMovingProducts' => $fastMoving,
            'slowMovingProducts' => ReportAnalytics::slowMoving(),
            'demandForecast' => ReportAnalytics::demandForecast(),
            'purchaseRecommendations' => $purchaseRecommendations,
            'highMarginProducts' => $highMargin,
            'trendingProducts' => ReportAnalytics::trending(),
            'hasCostPrice' => Schema::hasColumn('Products', 'cost_price'),
            'stockAlerts' => StockAlertNotifier::counts(),
            'stockThreshold' => (int) config('cms.stock_alert_threshold', 5),
            'aiConfigured' => AiReportInsights::isConfigured(),
            'aiInsights' => session('ai_report_insights'),
            'aiError' => session('ai_report_error'),
            'canEdit' => CmsAuth::canEdit(),
        ]);
    }

    public function generateAi(): RedirectResponse
    {
        $stats = AdminStats::dashboard();
        $lowStock = ReportAnalytics::lowStock();
        $outOfStock = ReportAnalytics::outOfStock();

        $result = AiReportInsights::generate([
            'stats' => $stats,
            'low_stock_count' => $lowStock->count(),
            'out_of_stock_count' => $outOfStock->count(),
            'low_stock_names' => $lowStock->pluck('name')->take(10)->all(),
            'out_of_stock_names' => $outOfStock->pluck('name')->take(10)->all(),
            'fast_moving' => collect(ReportAnalytics::fastMoving())->take(8)->all(),
            'purchase_recommendations' => collect(ReportAnalytics::purchaseRecommendations())->take(8)->all(),
            'high_margin' => collect(ReportAnalytics::highMargin())->take(8)->all(),
        ]);

        if ($result['ok']) {
            return redirect()->route('cms.reports')->with('ai_report_insights', $result['insights']);
        }

        return redirect()->route('cms.reports')->with('ai_report_error', $result['message']);
    }
}
