<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        static::ensureDomain($user);
    }

    public function updating(User $user): void
    {
        static::ensureDomain($user);
    }

    public static function ensureDomain(User $user): void
    {
        $email = (string) ($user->email ?? '');
        if ($email !== '') {
            $parts = explode('@', $email);
            $domain = strtolower($parts[1] ?? '');
            if ($domain !== '') {
                $user->email_domain = $domain;
            }
        }
    }
}