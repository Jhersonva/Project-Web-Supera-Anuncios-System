<?php

use App\Models\SystemSetting;

if (!function_exists('system_settings')) {
    function system_settings()
    {
        static $settings = null;

        if ($settings === null) {
            $settings = SystemSetting::first();
        }

        return $settings;
    }
}

if (!function_exists('system_company_name')) {
    function system_company_name($default = 'Mi Sistema')
    {
        return system_settings()?->company_name ?? $default;
    }
}

if (!function_exists('system_company_description')) {
    function system_company_description($default = '')
    {
        return system_settings()?->company_description ?? $default;
    }
}

if (!function_exists('system_logo')) {
    function system_logo($default = 'assets/img/logo/logo-supera-anuncios.jpeg')
    {
        $logo = system_settings()?->logo;

        return $logo ? asset($logo) : asset($default);
    }
}
