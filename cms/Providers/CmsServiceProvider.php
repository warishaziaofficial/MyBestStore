<?php

namespace Cms\Providers;

use Cms\Support\AdminNotifier;
use Cms\Support\CmsAuth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class CmsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(base_path('cms/views'), 'cms');

        Blade::if('authCms', fn () => CmsAuth::check());

        view()->composer('cms::layouts.admin', function ($view): void {
            $user = CmsAuth::user();
            $view->with([
                'isAdmin' => CmsAuth::isAdmin(),
                'canEdit' => CmsAuth::canEdit(),
                'cmsUserName' => $user?->username ?? session('cms_user_name'),
                'cmsUserEmail' => $user?->email,
                'cmsUserId' => $user?->id,
                'cmsUserRole' => $user?->role ?? CmsAuth::role(),
                'notificationCount' => AdminNotifier::unreadCount(),
                'recentNotifications' => AdminNotifier::recent(6),
            ]);
        });
    }
}
