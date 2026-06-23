<?php

namespace Cms\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class AiReportInsights
{
    public static function isConfigured(): bool
    {
        return trim((string) config('cms.openai_api_key', '')) !== '';
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public static function generate(array $context): array
    {
        if (! self::isConfigured()) {
            return [
                'ok' => false,
                'message' => 'Add OPENAI_API_KEY to your .env file to enable AI insights.',
                'insights' => null,
            ];
        }

        $prompt = self::buildPrompt($context);

        try {
            $response = Http::withToken((string) config('cms.openai_api_key'))
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('cms.openai_model', 'gpt-4o-mini'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a retail analytics advisor for MyBestStore, an electronics e-commerce store in Pakistan. Give concise, actionable insights in plain English. Use bullet points. Mention specific product names when provided. Keep under 350 words.',
                        ],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.4,
                    'max_tokens' => 700,
                ]);

            if (! $response->successful()) {
                return [
                    'ok' => false,
                    'message' => 'AI request failed: '.$response->json('error.message', 'Unknown error'),
                    'insights' => null,
                ];
            }

            $text = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

            return [
                'ok' => true,
                'message' => 'AI insights generated.',
                'insights' => $text,
                'generated_at' => now()->toIso8601String(),
            ];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'message' => 'AI service unavailable: '.$exception->getMessage(),
                'insights' => null,
            ];
        }
    }

    /** @param  array<string, mixed>  $context */
    private static function buildPrompt(array $context): string
    {
        $stats = $context['stats'] ?? [];
        $lines = [
            'Analyze this MyBestStore snapshot and recommend priorities for inventory, marketing and operations:',
            '',
            'Total products: '.($stats['total_products'] ?? 0),
            'Total orders: '.($stats['total_orders'] ?? 0),
            'Total revenue (PKR): '.($stats['total_revenue'] ?? 0),
            'Pending orders: '.($stats['pending_orders'] ?? 0),
            'Low stock count: '.($context['low_stock_count'] ?? 0),
            'Out of stock count: '.($context['out_of_stock_count'] ?? 0),
        ];

        if (! empty($context['low_stock_names'])) {
            $lines[] = 'Low stock products: '.implode(', ', $context['low_stock_names']);
        }

        if (! empty($context['out_of_stock_names'])) {
            $lines[] = 'Out of stock products: '.implode(', ', $context['out_of_stock_names']);
        }

        if (! empty($context['fast_moving'])) {
            $lines[] = 'Fast moving (30d): '.collect($context['fast_moving'])->pluck('product_name')->take(5)->implode(', ');
        }

        if (! empty($context['purchase_recommendations'])) {
            $lines[] = 'Purchase recommendations: '.collect($context['purchase_recommendations'])->take(5)->map(fn ($r) => $r['product_name'].' (order '.$r['recommended_qty'].')')->implode('; ');
        }

        if (Schema::hasColumn('Products', 'cost_price') && ! empty($context['high_margin'])) {
            $lines[] = 'High margin products: '.collect($context['high_margin'])->take(5)->pluck('product_name')->implode(', ');
        }

        $lines[] = '';
        $lines[] = 'Provide: (1) urgent actions, (2) restock priorities, (3) sales/marketing suggestions, (4) risk flags.';

        return implode("\n", $lines);
    }
}
