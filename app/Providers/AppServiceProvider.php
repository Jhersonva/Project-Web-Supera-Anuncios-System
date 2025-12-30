<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;  
use App\Models\SystemSetting;
use App\Models\PrivacyPolicySetting;
use Carbon\Carbon;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Carbon::setLocale('es');

        view()->composer('*', function ($view) {
            $view->with(
                'systemSettings',
                SystemSetting::first()
            );
        });

        view()->composer('*', function ($view) {
            $view->with(
                'policy',
                PrivacyPolicySetting::first()
            );
        });
    }
}
