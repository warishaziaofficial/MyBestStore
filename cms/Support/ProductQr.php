<?php

namespace Cms\Support;

use App\Services\BarcodeService;
use Cms\Models\Product;

class ProductQr
{
    /** Unique payload encoded inside each product QR label. */
    public static function code(?Product $product, ?int $productId = null): string
    {
        $id = $product?->id ?? $productId;

        if (! $id) {
            return '';
        }

        return 'MBS-PR-'.str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }

    public static function normalizeScanCode(string $code): string
    {
        $code = strtoupper(trim($code));

        return (string) preg_replace('/\s+/', '', $code);
    }

    public static function imageUrl(?Product $product, ?int $productId = null, int $size = 96): string
    {
        $code = self::code($product, $productId);

        if ($code === '') {
            return '';
        }

        return app(BarcodeService::class)->qrImageUrl($code, $size);
    }

    /** @return list<string> */
    public static function matchCodes(?Product $product, ?int $productId = null): array
    {
        $id = $product?->id ?? $productId;

        if (! $id) {
            return [];
        }

        $candidates = [
            self::code($product, $productId),
            DispatchWorkflow::productSku($product, $productId),
            DispatchWorkflow::productBarcode($product, $productId),
            'MBS-PRD-'.str_pad((string) $id, 5, '0', STR_PAD_LEFT),
            'PR-'.str_pad((string) $id, 5, '0', STR_PAD_LEFT),
            (string) $id,
            str_pad((string) $id, 3, '0', STR_PAD_LEFT),
            str_pad((string) $id, 5, '0', STR_PAD_LEFT),
        ];

        return array_values(array_unique(array_filter(array_map(
            fn (string $code) => self::normalizeScanCode($code),
            $candidates
        ))));
    }

    public static function scanMatches(string $rawScan, array $matchCodes): bool
    {
        $normalized = self::normalizeScanCode($rawScan);

        if ($normalized === '') {
            return false;
        }

        if (in_array($normalized, $matchCodes, true)) {
            return true;
        }

        foreach ($matchCodes as $code) {
            if (strcasecmp($normalized, $code) === 0) {
                return true;
            }
        }

        // Handheld scanners sometimes drop hyphens (MBS-PR-00020 → MBSPR00020).
        $compactScan = str_replace('-', '', $normalized);

        foreach ($matchCodes as $code) {
            if ($compactScan === str_replace('-', '', $code)) {
                return true;
            }
        }

        return false;
    }
}
