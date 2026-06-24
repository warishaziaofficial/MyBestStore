<?php

namespace App\Support;

use App\Models\Customer as StorefrontCustomer;
use Cms\Models\Customer as CmsCustomer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class PasswordResetNotifier
{
    public static function forgotPasswordRequested(string $email, ?string $name = null): void
    {
        $token = CustomerPasswordReset::createToken($email);
        $resetUrl = URL::route('customer.password.reset', ['token' => $token]);
        $cmsUrl = route('cms.customers.password-reset');

        $vars = [
            'customer_email' => $email,
            'customer_name' => $name ?: $email,
            'reset_url' => $resetUrl,
            'cms_password_reset_url' => $cmsUrl,
        ];

        if (config('notifications.customer_email', true)) {
            EmailTemplateMailer::sendOrFallback(
                'password_reset',
                $email,
                $vars,
                'Reset your MyBestStore password',
                '<p>Hi {{customer_name}},</p><p>Reset your password using this link (valid for 60 minutes):</p><p><a href="{{reset_url}}">{{reset_url}}</a></p>',
            );
        }

        if (config('notifications.admin_email', true)) {
            foreach (EmailTemplateMailer::adminRecipients() as $adminEmail) {
                EmailTemplateMailer::sendOrFallback(
                    'admin_password_reset_request',
                    $adminEmail,
                    $vars,
                    'Password reset requested — {{customer_email}}',
                    '<p>A customer requested a password reset on the storefront.</p><p><strong>Customer:</strong> {{customer_name}} ({{customer_email}})</p><p>Set a new password in CMS: <a href="{{cms_password_reset_url}}">{{cms_password_reset_url}}</a></p>',
                );
            }
        }
    }

    public static function adminSetPassword(string $email, string $plainPassword, ?string $adminName = null): void
    {
        $token = CustomerPasswordReset::createToken($email);
        $resetUrl = URL::route('customer.password.reset', ['token' => $token]);

        $vars = [
            'customer_email' => $email,
            'customer_name' => self::resolveCustomerName($email),
            'new_password' => $plainPassword,
            'reset_url' => $resetUrl,
            'admin_name' => $adminName ?: 'Admin',
            'cms_password_reset_url' => route('cms.customers.password-reset'),
        ];

        if (config('notifications.customer_email', true)) {
            EmailTemplateMailer::sendOrFallback(
                'customer_password_reset_by_admin',
                $email,
                $vars,
                'Your MyBestStore password was reset',
                '<p>Hi {{customer_name}},</p><p>An administrator reset your account password.</p><p><strong>New password:</strong> {{new_password}}</p><p>Sign in with this password, or choose your own using this link:</p><p><a href="{{reset_url}}">{{reset_url}}</a></p>',
            );
        }

        if (config('notifications.admin_email', true)) {
            foreach (EmailTemplateMailer::adminRecipients() as $adminEmail) {
                EmailTemplateMailer::sendOrFallback(
                    'admin_customer_password_reset',
                    $adminEmail,
                    $vars,
                    'Password reset completed — {{customer_email}}',
                    '<p><strong>{{admin_name}}</strong> reset the storefront password for <strong>{{customer_email}}</strong>.</p><p>The customer was emailed their new password and a self-service reset link.</p>',
                );
            }
        }
    }

    private static function resolveCustomerName(string $email): string
    {
        if (Schema::hasTable('Customers')) {
            $name = CmsCustomer::query()->where('email', $email)->value('name');

            if ($name) {
                return (string) $name;
            }
        }

        $name = StorefrontCustomer::query()->where('email', $email)->value('name');

        return $name ? (string) $name : $email;
    }
}
