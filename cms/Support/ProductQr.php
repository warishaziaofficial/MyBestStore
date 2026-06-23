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
        return array_values(array_unique(array_filter([
            self::code($product, $productId),
            DispatchWorkflow::productSku($product, $productId),
            DispatchWorkflow::productBarcode($product, $productId),
        ])));
    }
}
