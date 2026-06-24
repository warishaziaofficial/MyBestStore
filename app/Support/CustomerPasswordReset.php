<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CustomerPasswordReset
{
    private const TABLE = 'CustomerPasswordResets';

    private const EXPIRE_MINUTES = 60;

    public static function createToken(string $email): string
    {
        $plainToken = Str::random(64);

        if (! Schema::hasTable(self::TABLE)) {
            return $plainToken;
        }

        DB::table(self::TABLE)->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($plainToken),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return $plainToken;
    }

    public static function findEmailByToken(string $plainToken): ?string
    {
        if (! Schema::hasTable(self::TABLE)) {
            return null;
        }

        foreach (DB::table(self::TABLE)->get() as $row) {
            if (! Hash::check($plainToken, $row->token)) {
                continue;
            }

            if (now()->subMinutes(self::EXPIRE_MINUTES)->greaterThan($row->created_at)) {
                return null;
            }

            return $row->email;
        }

        return null;
    }

    public static function delete(string $email): void
    {
        if (Schema::hasTable(self::TABLE)) {
            DB::table(self::TABLE)->where('email', $email)->delete();
        }
    }
}
