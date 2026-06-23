<?php

namespace Cms\Support;

use App\Mail\OrderInvoiceMail;
use App\Models\Order as StorefrontOrder;
use App\Support\EmailTemplateMailer;
use App\Support\OrderPresenter;
use Cms\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StoreNotifier
{
    public static function orderPlaced(StorefrontOrder|Order $order, ?string $paymentLabel = null): void
    {
        $vars = self::orderVars($order, $paymentLabel);

        AdminNotifier::push(
            'new_order',
            'New order '.$vars['order_number'],
            $vars['customer_name'].' — Rs '.$vars['total'].($paymentLabel ? ' ('.$paymentLabel.')' : ''),
            $vars['cms_order_url'],
            false
        );

        self::adminEmail('admin_new_order', $vars);
        self::adminSms('admin_new_order', $vars);

        self::customerEmail('customer_order_placed', $order, $vars);
        self::customerInvoice($order);
        self::customerSms('customer_order_placed', $order, $vars);
    }

    public static function orderStatusChanged(Order $order, string $previousStatus, string $newStatus): void
    {
        if ($previousStatus === $newStatus) {
            return;
        }

        $vars = self::orderVars($order);

        $customerSlug = match ($newStatus) {
            'confirmed' => 'customer_order_confirmed',
            'processing' => 'customer_order_processing',
            'shipped' => 'customer_order_shipped',
            'delivered' => 'customer_order_delivered',
            'cancelled' => 'customer_order_cancelled',
            default => null,
        };

        if ($customerSlug) {
            self::customerEmail($customerSlug, $order, $vars);

            if ($newStatus === 'shipped') {
                self::customerSms('customer_order_shipped', $order, $vars);
            } elseif ($newStatus === 'delivered') {
                self::customerSms('customer_order_delivered', $order, $vars);
            } elseif ($newStatus === 'cancelled') {
                self::customerSms('customer_order_cancelled', $order, $vars);
            }
        }

        $adminSlug = match ($newStatus) {
            'shipped' => 'admin_order_shipped',
            'cancelled' => 'admin_order_cancelled',
            default => null,
        };

        if ($adminSlug) {
            self::adminEmail($adminSlug, $vars);

            if ($newStatus === 'shipped') {
                AdminNotifier::push(
                    'order_shipped',
                    'Order shipped '.$vars['order_number'],
                    ($vars['courier_name'] ?: 'Courier').' · '.($vars['tracking_number'] ?: 'No tracking'),
                    $vars['cms_order_url'],
                    false
                );
                self::adminSms('admin_order_shipped', $vars);
            }
        }
    }

    public static function orderDispatched(Order $order): void
    {
        $order->loadMissing(['items.product']);
        $vars = self::orderVars($order);

        AdminNotifier::push(
            'order_dispatched',
            'Order dispatched '.$vars['order_number'],
            ($vars['courier_name'] ?: 'Courier').' · '.($vars['tracking_number'] ?: 'No tracking'),
            $vars['cms_order_url'],
            false
        );

        self::customerEmail('customer_order_shipped', $order, $vars);
        self::customerSms('customer_order_shipped', $order, $vars);
        self::adminEmail('admin_order_shipped', $vars);
        self::adminSms('admin_order_shipped', $vars);
    }

    /** @return array<string, string> */
    public static function orderVars(StorefrontOrder|Order $order, ?string $paymentLabel = null): array
    {
        $order->loadMissing('items');

        $itemsSummary = $order->items
            ->map(fn ($item) => $item->product_name.' × '.$item->quantity)
            ->implode(', ');

        $trackingNumber = self::resolveTrackingNumber($order);
        $courierName = self::resolveCourierName($order);

        return [
            'customer_name' => (string) ($order->customer_name ?? 'Customer'),
            'customer_email' => (string) ($order->customer_email ?? ''),
            'customer_phone' => (string) ($order->customer_phone ?? ''),
            'order_number' => (string) ($order->order_number ?? ''),
            'status' => OrderPresenter::statusLabel((string) ($order->order_status ?? 'pending')),
            'payment_status' => OrderPresenter::paymentStatusLabel((string) ($order->payment_status ?? 'pending')),
            'payment_method' => $paymentLabel ?? OrderPresenter::paymentLabel((string) ($order->payment_method ?? 'cod')),
            'total' => number_format((int) ($order->total_amount ?? $order->total ?? 0)),
            'subtotal' => number_format((int) ($order->subtotal ?? 0)),
            'shipping' => number_format((int) ($order->shipping_amount ?? $order->shipping ?? 0)),
            'shipping_address' => OrderPresenter::shippingAddress($order),
            'tracking_number' => $trackingNumber ?: '—',
            'courier_name' => $courierName ?: '—',
            'items_summary' => $itemsSummary ?: '—',
            'track_url' => route('order.track', ['order_number' => $order->order_number]),
            'cms_order_url' => url('/cms/orders/'.$order->id),
        ];
    }

    /** @param array<string, string> $vars */
    private static function customerEmail(string $slug, StorefrontOrder|Order $order, array $vars): void
    {
        if (! config('notifications.customer_email', true)) {
            return;
        }

        $email = trim($vars['customer_email']);

        if ($email === '') {
            return;
        }

        EmailTemplateMailer::send($slug, $email, $vars);
    }

    /** @param array<string, string> $vars */
    private static function adminEmail(string $slug, array $vars): void
    {
        if (! config('notifications.admin_email', true)) {
            return;
        }

        foreach (EmailTemplateMailer::adminRecipients() as $email) {
            EmailTemplateMailer::send($slug, $email, $vars);
        }
    }

    /** @param array<string, string> $vars */
    private static function customerSms(string $slug, StorefrontOrder|Order $order, array $vars): void
    {
        if (! config('notifications.customer_sms', false)) {
            return;
        }

        $phone = trim((string) ($order->customer_phone ?? ''));

        if ($phone === '') {
            return;
        }

        $message = config('notifications.sms.'.$slug);

        if (! is_string($message) || $message === '') {
            return;
        }

        SmsNotifier::sendTo($phone, EmailTemplateMailer::render($message, $vars));
    }

    /** @param array<string, string> $vars */
    private static function adminSms(string $slug, array $vars): void
    {
        if (! config('notifications.admin_sms', true)) {
            return;
        }

        $message = config('notifications.sms.'.$slug);

        if (! is_string($message) || $message === '') {
            return;
        }

        SmsNotifier::send(EmailTemplateMailer::render($message, $vars));
    }

    private static function customerInvoice(StorefrontOrder|Order $order): void
    {
        $email = trim((string) ($order->customer_email ?? ''));

        if ($email === '') {
            return;
        }

        try {
            Mail::to($email)->send(new OrderInvoiceMail($order));
        } catch (\Throwable $exception) {
            Log::error('Order invoice email failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private static function resolveTrackingNumber(StorefrontOrder|Order $order): ?string
    {
        if ($order instanceof Order) {
            return DispatchWorkflow::trackingNumber($order);
        }

        return filled($order->tracking_number ?? null) ? (string) $order->tracking_number : null;
    }

    private static function resolveCourierName(StorefrontOrder|Order $order): ?string
    {
        if ($order instanceof Order) {
            return DispatchWorkflow::courierName($order);
        }

        return filled($order->courier_name ?? null) ? (string) $order->courier_name : null;
    }
}
