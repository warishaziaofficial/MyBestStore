<?php

namespace App\Support;

use App\Models\Order;

class OrderPresenter
{
    public static function paymentLabel(string $method): string
    {
        return match ($method) {
            'cash_on_delivery', 'cod' => 'Cash on Delivery',
            'jazzcash' => 'JazzCash',
            default => ucfirst(str_replace('_', ' ', $method)),
        };
    }

    public static function paymentStatusLabel(string $status): string
    {
        return match ($status) {
            'unpaid' => 'Unpaid',
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public static function shippingStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Pending',
            'packed' => 'Packed',
            'dispatched' => 'Dispatched',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'returned' => 'Returned',
            default => $status ? ucfirst(str_replace('_', ' ', $status)) : 'Pending',
        };
    }

    public static function trackingUrl(Order $order): ?string
    {
        if (! filled($order->tracking_number)) {
            return null;
        }

        if ($order->relationLoaded('courierCompany') && $order->courierCompany) {
            return $order->courierCompany->trackingLink($order->tracking_number);
        }

        if ($order->courier_company_id) {
            $order->loadMissing('courierCompany');

            return $order->courierCompany?->trackingLink($order->tracking_number);
        }

        return null;
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'packed' => 'Packed',
            'dispatched' => 'Dispatched',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * @return array<int, array{key: string, label: string, state: string}>
     */
    public static function trackingTimeline(Order $order): array
    {
        $steps = [
            ['key' => 'pending', 'label' => 'Pending'],
            ['key' => 'confirmed', 'label' => 'Confirmed'],
            ['key' => 'processing', 'label' => 'Processing'],
            ['key' => 'packed', 'label' => 'Packed'],
            ['key' => 'dispatched', 'label' => 'Dispatched'],
            ['key' => 'shipped', 'label' => 'Shipped'],
            ['key' => 'delivered', 'label' => 'Delivered'],
        ];

        $currentKey = self::resolveTrackingStep($order);
        $currentIndex = 0;

        foreach ($steps as $index => $step) {
            if ($step['key'] === $currentKey) {
                $currentIndex = $index;
                break;
            }
        }

        return array_map(function (array $step, int $index) use ($currentIndex, $order) {
            if ($order->order_status === 'cancelled') {
                return [...$step, 'state' => 'cancelled'];
            }

            if ($index < $currentIndex) {
                return [...$step, 'state' => 'completed'];
            }

            if ($index === $currentIndex) {
                return [...$step, 'state' => 'current'];
            }

            return [...$step, 'state' => 'upcoming'];
        }, $steps, array_keys($steps));
    }

    public static function resolveTrackingStep(Order $order): string
    {
        if ($order->order_status === 'cancelled') {
            return 'pending';
        }

        if ($order->order_status === 'delivered' || $order->shipping_status === 'delivered') {
            return 'delivered';
        }

        if ($order->order_status === 'shipped' || $order->shipping_status === 'in_transit') {
            return 'shipped';
        }

        if ($order->shipping_status === 'dispatched' || $order->order_status === 'dispatched') {
            return 'dispatched';
        }

        if ($order->shipping_status === 'packed' || $order->order_status === 'packed') {
            return 'packed';
        }

        if ($order->order_status === 'processing') {
            return 'processing';
        }

        if (in_array($order->order_status, ['confirmed', 'paid'], true)) {
            return 'confirmed';
        }

        return 'pending';
    }

    public static function shippingAddress(Order $order): string
    {
        $parts = array_filter([
            $order->shipping_address,
            $order->city,
            $order->province,
            $order->country,
            $order->postal_code,
        ]);

        return implode(', ', $parts);
    }

    public static function absoluteAsset(?string $path): string
    {
        if (empty($path)) {
            return asset('placeholder-product.svg');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url(ltrim($path, '/'));
    }

    public static function supportMessage(): string
    {
        $phone = config('storefront.contact_cards.0.value', '+92 300 1234567');
        $email = config('storefront.contact_cards.1.value', 'orders@mybeststore.pk');

        return "For support, contact MyBestStore support team at {$email} or {$phone}.";
    }

    /**
     * Dompdf image embedding requires the PHP GD extension.
     * Enable GD in php.ini to include logo/product images in invoice PDFs.
     */
    public static function pdfCanEmbedImages(): bool
    {
        return extension_loaded('gd');
    }

    public static function pdfImageDataUri(?string $path): ?string
    {
        if (! self::pdfCanEmbedImages() || empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }

        $localPath = public_path(ltrim($path, '/'));

        if (! is_file($localPath)) {
            return null;
        }

        $extension = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => null,
        };

        if (! $mime) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($localPath));
    }
}
