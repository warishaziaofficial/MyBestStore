<?php

namespace Cms\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNotifier
{
    public static function send(string $message): bool
    {
        $phone = trim((string) config('cms.sms_admin_phone', ''));

        if ($phone === '') {
            return false;
        }

        return self::sendTo($phone, $message);
    }

    public static function sendTo(string $phone, string $message): bool
    {
        $phone = trim($phone);
        $message = trim($message);

        if ($phone === '' || $message === '') {
            return false;
        }

        return match (config('cms.sms_driver', 'log')) {
            'http' => self::sendHttp($phone, $message),
            'twilio' => self::sendTwilio($phone, $message),
            default => self::sendLog($phone, $message),
        };
    }

    private static function sendLog(string $phone, string $message): bool
    {
        Log::channel('single')->info('[SMS to '.$phone.'] '.$message);

        return true;
    }

    private static function sendHttp(string $phone, string $message): bool
    {
        $url = (string) config('cms.sms_webhook_url', '');

        if ($url === '') {
            return self::sendLog($phone, $message);
        }

        try {
            $response = Http::timeout(10)->post($url, [
                'phone' => $phone,
                'message' => $message,
            ]);

            return $response->successful();
        } catch (\Throwable $exception) {
            Log::warning('SMS webhook failed: '.$exception->getMessage());

            return false;
        }
    }

    private static function sendTwilio(string $phone, string $message): bool
    {
        $sid = (string) config('cms.twilio_sid', '');
        $token = (string) config('cms.twilio_token', '');
        $from = (string) config('cms.twilio_from', '');

        if ($sid === '' || $token === '' || $from === '') {
            return self::sendLog($phone, $message);
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To' => $phone,
                    'Body' => $message,
                ]);

            return $response->successful();
        } catch (\Throwable $exception) {
            Log::warning('Twilio SMS failed: '.$exception->getMessage());

            return false;
        }
    }
}
