<?php

namespace Cms\Http\Controllers;

use Cms\Models\SocialAccount;
use Cms\Support\CmsAuth;
use Cms\Support\SocialOrderImporter;
use Cms\Support\SocialPlatforms;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SocialIntegrationController extends Controller
{
    public function index(): View
    {
        $accounts = Schema::hasTable('SocialAccounts')
            ? SocialAccount::query()->orderBy('platform')->orderBy('id')->get()
            : collect();

        return view('cms::social.index', [
            'accounts' => $accounts,
            'platforms' => SocialPlatforms::ACCOUNT_PLATFORMS,
            'canEdit' => CmsAuth::canEdit(),
        ]);
    }

    public function sync(int $id): RedirectResponse
    {
        $this->requireEditor();

        $account = SocialAccount::query()->findOrFail($id);

        if ($account->status !== 'connected') {
            return back()->with('success', 'Connect this account first before syncing orders.');
        }

        $account->update(['last_sync_at' => now()]);

        return back()->with('success', 'Sync ready for '.$account->platform.'. Connect live API to import orders automatically.');
    }

    public function testWebhook(Request $request): RedirectResponse
    {
        $this->requireEditor();

        $platform = $request->input('platform', 'instagram');
        $payload = SocialOrderImporter::samplePayload($platform);
        $result = SocialOrderImporter::import($payload, 'test');

        return back()->with(
            'success',
            $result['message'] ?? 'Test webhook processed.'
        );
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }
    }
}
