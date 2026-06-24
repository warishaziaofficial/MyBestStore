<?php

namespace App\Services;

use App\Models\Customer;
use App\Support\ChatwootSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrmContactSync
{
    public function syncSignup(Customer $customer): bool
    {
        if (! config('crm.enabled', false)) {
            return false;
        }

        $baseUrl = trim((string) (ChatwootSettings::resolved()['crm_url'] ?: config('crm.base_url', '')));
        $apiKey = trim((string) config('crm.api_key', ''));

        if ($baseUrl === '' || $apiKey === '') {
            Log::warning('CRM signup sync skipped: CRM_BASE_URL or CRM_INBOUND_API_KEY not configured.');

            return false;
        }

        $endpoint = rtrim($baseUrl, '/').'/api/ecommerce_inbound.php';

        $payload = [
            'event' => 'ecommerce_signup',
            'name' => (string) $customer->name,
            'email' => (string) $customer->email,
            'phone' => (string) ($customer->phone ?? ''),
            'ecommerce_customer_id' => (int) $customer->id,
            'source' => (string) config('crm.source', 'MyBestStore Website'),
            'status' => 'lead',
            'registered_at' => $customer->created_at?->toIso8601String() ?? now()->toIso8601String(),
            'store_url' => (string) config('app.url'),
        ];

        try {
            $response = Http::timeout((int) config('crm.timeout', 10))
                ->acceptJson()
                ->withHeaders([
                    'X-Webhook-Secret' => $apiKey,
                    'X-CRM-Inbound-Key' => $apiKey,
                ])
                ->post($endpoint, $payload);

            if (! $response->successful()) {
                Log::warning('CRM signup sync failed', [
                    'customer_id' => $customer->id,
                    'email' => $customer->email,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable $exception) {
            Log::error('CRM signup sync error', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
