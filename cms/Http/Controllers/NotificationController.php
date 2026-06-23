<?php

namespace Cms\Http\Controllers;

use Cms\Support\AdminNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        return view('cms::notifications.index', [
            'notifications' => AdminNotifier::recent(50),
            'unreadCount' => AdminNotifier::unreadCount(),
        ]);
    }

    public function poll(Request $request): JsonResponse
    {
        $sinceId = max(0, (int) $request->query('since_id', 0));

        return response()->json(AdminNotifier::poll($sinceId));
    }

    public function markRead(Request $request, int $id): RedirectResponse
    {
        AdminNotifier::markRead($id);

        $redirect = $request->input('redirect');

        if ($redirect && str_starts_with($redirect, url('/cms'))) {
            return redirect($redirect);
        }

        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        AdminNotifier::markAllRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
