<?php

namespace App\Support;

use App\Models\Setting;

class AdPrices
{
    public static function all(): array
    {
        return [
            'urgent'    => (float) Setting::get('urgent_publication_price'),
            'featured'  => (float) Setting::get('featured_publication_price'),
            'premiere'  => (float) Setting::get('premiere_publication_price'),
            'semi_new'  => (float) Setting::get('semi_new_publication_price'),
            'new'       => (float) Setting::get('new_publication_price'),
            'available' => (float) Setting::get('available_publication_price'),
            'top'       => (float) Setting::get('top_publication_price'),
        ];
    }
}
