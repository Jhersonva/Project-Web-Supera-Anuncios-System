<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SystemSetting;
use App\Models\PrivacyPolicySetting;
use App\Models\User;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        Carbon::setLocale('es');

        view()->composer('*', function ($view) {

            // Configuración del sistema
            $view->with('systemSettings', SystemSetting::first());

            // Políticas de privacidad
            $view->with('policy', PrivacyPolicySetting::first());

            // Cumpleaños
            if (auth()->check()) {
                $birthdays = User::whereNotNull('birthdate')
                    ->whereMonth('birthdate', now()->month)
                    ->whereDay('birthdate', now()->day)
                    ->get();
            } else {
                $birthdays = collect();
            }

            $view->with('birthdays', $birthdays);
        });
    }
}
