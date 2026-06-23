<?php

namespace Cms\Support;

use Cms\Models\User;

class CmsAuth
{
    public static function check(): bool
    {
        return session()->has('cms_user_id');
    }

    public static function user(): ?User
    {
        if (! self::check()) {
            return null;
        }

        return User::find(session('cms_user_id'));
    }

    public static function role(): ?string
    {
        return session('cms_user_role');
    }

    public static function login(User $user): void
    {
        session([
            'cms_user_id' => $user->id,
            'cms_user_role' => $user->role,
            'cms_user_name' => $user->username,
        ]);
    }

    public static function logout(): void
    {
        session()->forget(['cms_user_id', 'cms_user_role', 'cms_user_name']);
    }

    public static function canEdit(): bool
    {
        return in_array(self::role(), ['admin', 'editor'], true);
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }
}
