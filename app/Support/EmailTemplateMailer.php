<?php

namespace App\Support;

use Cms\Models\EmailTemplate;
use Cms\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class EmailTemplateMailer
{
    public static function adminEmail(): ?string
    {
        $fromConfig = trim((string) config('notifications.admin_email_address', ''));

        return $fromConfig !== '' ? $fromConfig : null;
    }

    /** @return list<string> */
    public static function adminRecipients(): array
    {
        $fromEnv = array_filter(array_map('trim', explode(',', (string) config('cms.stock_alert_emails', ''))));

        if ($fromEnv !== []) {
            return array_values($fromEnv);
        }

        if (Schema::hasTable('Users')) {
            $admins = User::query()->where('role', 'admin')->pluck('email')->filter()->values()->all();

            if ($admins !== []) {
                return $admins;
            }
        }

        return array_filter([self::adminEmail()]);
    }

    public static function send(string $slug, string $to, array $vars = []): bool
    {
        $template = self::resolveTemplate($slug);

        if (! $template) {
            return false;
        }

        return self::deliver($to, self::render($template['subject'], $vars), self::render($template['body'], $vars));
    }

    public static function sendOrFallback(
        string $slug,
        string $to,
        array $vars,
        string $fallbackSubject,
        string $fallbackBody,
    ): bool {
        $template = self::resolveTemplate($slug);

        if ($template) {
            return self::deliver(
                $to,
                self::render($template['subject'], $vars),
                self::render($template['body'], $vars),
            );
        }

        return self::deliver($to, self::render($fallbackSubject, $vars), self::render($fallbackBody, $vars));
    }

    public static function render(string $text, array $vars): string
    {
        $replacements = [];

        foreach ($vars as $key => $value) {
            $replacements['{{'.$key.'}}'] = (string) $value;
        }

        return strtr($text, $replacements);
    }

    /** @return array{subject: string, body: string}|null */
    private static function resolveTemplate(string $slug): ?array
    {
        if (Schema::hasTable('EmailTemplates')) {
            $row = EmailTemplate::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if ($row) {
                return [
                    'subject' => (string) $row->subject,
                    'body' => (string) $row->body,
                ];
            }
        }

        $defaults = config('notifications.templates.'.$slug);

        if (! is_array($defaults)) {
            return null;
        }

        return [
            'subject' => (string) ($defaults['subject'] ?? ''),
            'body' => (string) ($defaults['body'] ?? ''),
        ];
    }

    private static function deliver(string $to, string $subject, string $body): bool
    {
        $to = trim($to);

        if ($to === '' || $subject === '') {
            return false;
        }

        try {
            Mail::send('emails.template', [
                'subject' => $subject,
                'body' => $body,
            ], function ($message) use ($to, $subject): void {
                $message->to($to)->subject($subject);
            });

            return true;
        } catch (\Throwable $exception) {
            Log::error('Email delivery failed', [
                'to' => $to,
                'subject' => $subject,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
