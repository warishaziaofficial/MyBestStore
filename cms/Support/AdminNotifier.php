<?php

namespace Cms\Support;

use Cms\Models\AdminNotification;
use Illuminate\Support\Facades\Schema;

class AdminNotifier
{
    /** @var list<string> */
    private const SMS_TYPES = ['out_of_stock', 'new_order', 'new_inquiry'];

    public static function push(string $type, string $title, string $body, ?string $link = null, bool $sendSms = false): void
    {
        if (Schema::hasTable('AdminNotifications')) {
            AdminNotification::create([
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'link' => $link,
                'is_read' => false,
            ]);
        }

        if ($sendSms || in_array($type, self::SMS_TYPES, true)) {
            SmsNotifier::send($title.' — '.$body);
        }
    }

    public static function unreadCount(): int
    {
        if (! Schema::hasTable('AdminNotifications')) {
            return 0;
        }

        return AdminNotification::query()->where('is_read', false)->count();
    }

    public static function recent(int $limit = 8)
    {
        if (! Schema::hasTable('AdminNotifications')) {
            return collect();
        }

        return AdminNotification::query()
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /** @return array{unread_count: int, latest_id: int, new: list<array<string, mixed>>, recent: list<array<string, mixed>>} */
    public static function poll(int $sinceId = 0, int $recentLimit = 8): array
    {
        if (! Schema::hasTable('AdminNotifications')) {
            return [
                'unread_count' => 0,
                'latest_id' => 0,
                'new' => [],
                'recent' => [],
            ];
        }

        $latestId = (int) (AdminNotification::query()->max('id') ?? 0);

        $new = AdminNotification::query()
            ->where('id', '>', max(0, $sinceId))
            ->orderBy('id')
            ->get()
            ->map(fn (AdminNotification $notification) => self::format($notification))
            ->values()
            ->all();

        $recent = self::recent($recentLimit)
            ->map(fn (AdminNotification $notification) => self::format($notification))
            ->values()
            ->all();

        return [
            'unread_count' => self::unreadCount(),
            'latest_id' => $latestId,
            'new' => $new,
            'recent' => $recent,
        ];
    }

    /** @return array<string, mixed> */
    public static function format(AdminNotification $notification): array
    {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $notification->title,
            'body' => $notification->body,
            'link' => $notification->link,
            'is_read' => (bool) $notification->is_read,
            'created_at' => $notification->created_at?->toIso8601String(),
            'time_ago' => $notification->created_at?->diffForHumans(),
        ];
    }

    public static function markRead(int $id): void
    {
        if (! Schema::hasTable('AdminNotifications')) {
            return;
        }

        AdminNotification::query()->whereKey($id)->update(['is_read' => true]);
    }

    public static function markAllRead(): void
    {
        if (! Schema::hasTable('AdminNotifications')) {
            return;
        }

        AdminNotification::query()->where('is_read', false)->update(['is_read' => true]);
    }
}
