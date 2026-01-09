<?php

namespace App\Helpers;

use App\Models\User;
use Carbon\Carbon;

class BirthdayHelper
{
    public static function todayBirthdays()
    {
        return User::whereNotNull('birthdate')
            ->whereMonth('birthdate', now()->month)
            ->whereDay('birthdate', now()->day)
            ->get();
    }
}