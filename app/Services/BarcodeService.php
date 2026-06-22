<?php

namespace App\Services;

use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeService
{
    public function makeOrderBarcode(int $orderId): string
    {
        return 'MBS-ORD-'.str_pad((string) $orderId, 5, '0', STR_PAD_LEFT);
    }

    public function makeProductBarcode(Product $product): string
    {
        if (filled($product->barcode)) {
            return (string) $product->barcode;
        }

        if (filled($product->sku)) {
            return 'MBS-SKU-'.strtoupper((string) $product->sku);
        }

        return 'MBS-PRD-'.str_pad((string) $product->id, 5, '0', STR_PAD_LEFT);
    }

    public function barcodeSvg(string $code, int $height = 60, int $widthFactor = 2): string
    {
        $generator = new BarcodeGeneratorSVG;

        return $generator->getBarcode($code, $generator::TYPE_CODE_128, $widthFactor, $height);
    }

    public function qrImageUrl(string $code, int $size = 140): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size='.$size.'x'.$size.'&data='.urlencode($code);
    }
}
