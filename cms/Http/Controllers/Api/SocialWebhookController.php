<?php

namespace Cms\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Cms\Models\SocialAccount;
use Cms\Models\SocialSyncLog;
use Cms\Support\AdminApiFormatter;
use Cms\Support\SocialOrderImporter;
use Cms\Support\SocialPlatforms;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SocialWebhookController extends Controller
{
    /**
     * Public webhook endpoint for future Instagram / Facebook / TikTok / WhatsApp integrations.
     */
    public function receive(Request $request, string $platform): JsonResponse
    {
        if (! Schema::hasTable('Orders')) {
            return response()->json(['message' => 'Orders are not available.'], 503);
        }

        $platform = SocialPlatforms::normalizePlatform($platform);

        if ($platform === 'website') {
            return response()->json(['message' => 'Use website checkout for storefront orders.'], 422);
        }

        $account = $this->resolveAccount($request, $platform);

        if ($account && $account->webhook_secret) {
            $providedSecret = $request->header('X-Webhook-Secret') ?? $request->input('webhook_secret');

            if ($providedSecret !== $account->webhook_secret) {
                return response()->json(['message' => 'Invalid webhook secret.'], 401);
            }
        }

        $payload = $request->all();

        if ($payload === []) {
            return response()->json(['message' => 'Webhook payload is empty.'], 422);
        }

        $payload['platform'] = $platform;

        if ($account && empty($payload['external_account_id'])) {
            $payload['external_account_id'] = $account->account_id;
        }

        $result = SocialOrderImporter::import($payload, 'webhook');

        $statusCode = match ($result['status']) {
            'success' => 201,
            'duplicate' => 200,
            default => 200,
        };

        return response()->json([
            'message' => $result['message'],
            'status' => $result['status'],
            'data' => [
                'order' => isset($result['order']) ? AdminApiFormatter::order($result['order'], true) : null,
                'log_id' => $result['log']->id ?? null,
            ],
        ], $statusCode);
    }

    public function logs(Request $request): JsonResponse
    {
        if (! Schema::hasTable('SocialSyncLogs')) {
            return response()->json(['data' => [], 'meta' => ['total' => 0]]);
        }

        $query = SocialSyncLog::query()->orderByDesc('id');

        if ($request->filled('platform')) {
            $query->where('platform', SocialPlatforms::normalizePlatform((string) $request->query('platform')));
        }

        $paginator = $query->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'data' => collect($paginator->items())->map(fn (SocialSyncLog $log) => [
                'id' => $log->id,
                'social_account_id' => $log->social_account_id,
                'platform' => $log->platform,
                'trigger_type' => $log->trigger_type,
                'status' => $log->status,
                'orders_imported' => (int) $log->orders_imported,
                'external_order_id' => $log->external_order_id,
                'order_id' => $log->order_id,
                'message' => $log->message,
                'created_at' => $log->created_at?->toIso8601String(),
            ])->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    private function resolveAccount(Request $request, string $platform): ?SocialAccount
    {
        if (! Schema::hasTable('SocialAccounts')) {
            return null;
        }

        $accountId = $request->header('X-Account-Id') ?? $request->input('external_account_id');

        if ($accountId) {
            return SocialAccount::query()
                ->where('platform', $platform)
                ->where('account_id', $accountId)
                ->first();
        }

        return SocialAccount::query()
            ->where('platform', $platform)
            ->where('status', 'connected')
            ->orderBy('id')
            ->first();
    }
}
