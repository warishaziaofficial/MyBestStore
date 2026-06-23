<?php

namespace Cms\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Cms\Models\SocialAccount;
use Cms\Support\AdminApiFormatter;
use Cms\Support\CmsAuth;
use Cms\Support\SocialOrderImporter;
use Cms\Support\SocialPlatforms;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SocialAccountApiController extends Controller
{
    public function index(): JsonResponse
    {
        if (! Schema::hasTable('SocialAccounts')) {
            return response()->json([
                'data' => [],
                'message' => 'Import cms/SocialAccounts.sql to enable social integration structure.',
            ]);
        }

        $accounts = SocialAccount::query()->orderBy('platform')->orderBy('id')->get();

        return response()->json([
            'data' => $accounts->map(fn (SocialAccount $account) => $this->formatAccount($account))->values(),
            'meta' => [
                'platforms' => SocialPlatforms::ACCOUNT_PLATFORMS,
                'order_sources' => SocialPlatforms::ORDER_SOURCES,
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $account = SocialAccount::query()->findOrFail($id);

        return response()->json(['data' => $this->formatAccount($account)]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->requireEditor();
        $account = SocialAccount::create($this->validated($request));

        return response()->json([
            'message' => 'Social account saved.',
            'data' => $this->formatAccount($account),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->requireEditor();
        $account = SocialAccount::query()->findOrFail($id);
        $account->update($this->validated($request, $account));

        return response()->json([
            'message' => 'Social account updated.',
            'data' => $this->formatAccount($account->fresh()),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->requireEditor();
        SocialAccount::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Social account removed.']);
    }

    public function sync(int $id): JsonResponse
    {
        $this->requireEditor();

        $account = SocialAccount::query()->findOrFail($id);

        if ($account->status !== 'connected') {
            return response()->json([
                'message' => 'Account is not connected. Connect the platform API before syncing.',
                'data' => $this->formatAccount($account),
            ], 422);
        }

        return response()->json([
            'message' => 'Manual sync endpoint is ready. Platform API integration will pull orders here in a future module.',
            'data' => $this->formatAccount($account),
            'meta' => [
                'webhook_url' => url('/api/webhooks/social/'.$account->platform),
                'next_step' => 'Send orders to the webhook URL or use POST /cms/api/social/test-webhook for testing.',
            ],
        ]);
    }

    public function testWebhook(Request $request): JsonResponse
    {
        $this->requireEditor();

        $data = $request->validate([
            'platform' => ['nullable', 'string'],
            'payload' => ['nullable', 'array'],
        ]);

        $payload = $data['payload'] ?? SocialOrderImporter::samplePayload($data['platform'] ?? 'instagram');

        $result = SocialOrderImporter::import($payload, 'test');

        return response()->json([
            'message' => $result['message'],
            'status' => $result['status'],
            'data' => [
                'order' => isset($result['order']) ? AdminApiFormatter::order($result['order'], true) : null,
                'log_id' => $result['log']->id ?? null,
            ],
        ], $result['status'] === 'success' ? 201 : 200);
    }

    private function validated(Request $request, ?SocialAccount $account = null): array
    {
        return $request->validate([
            'platform' => [$account ? 'sometimes' : 'required', 'in:'.implode(',', SocialPlatforms::ACCOUNT_PLATFORMS)],
            'account_name' => [$account ? 'sometimes' : 'required', 'string', 'max:150'],
            'account_id' => [$account ? 'sometimes' : 'required', 'string', 'max:100'],
            'status' => ['nullable', 'in:connected,disconnected,error'],
            'webhook_secret' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function formatAccount(SocialAccount $account): array
    {
        return [
            'id' => $account->id,
            'platform' => $account->platform,
            'account_name' => $account->account_name,
            'account_id' => $account->account_id,
            'status' => $account->status,
            'orders_synced_count' => (int) $account->orders_synced_count,
            'last_sync_at' => $account->last_sync_at?->toIso8601String(),
            'webhook_url' => url('/api/webhooks/social/'.$account->platform),
            'notes' => $account->notes,
            'created_at' => $account->created_at?->toIso8601String(),
            'updated_at' => $account->updated_at?->toIso8601String(),
        ];
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }
}
