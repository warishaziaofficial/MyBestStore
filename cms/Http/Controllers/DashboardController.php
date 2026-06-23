<?php

namespace Cms\Http\Controllers;

use Cms\Support\AdminStats;
use Cms\Support\CmsAuth;
use Cms\Support\StockAlertNotifier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('cms::dashboard', [
            'kpis' => AdminStats::dashboard(),
            'stockAlerts' => StockAlertNotifier::counts(),
            'canEdit' => CmsAuth::canEdit(),
            'isAdmin' => CmsAuth::isAdmin(),
        ]);
    }
}
