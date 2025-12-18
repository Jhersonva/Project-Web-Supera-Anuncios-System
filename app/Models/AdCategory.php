<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCategory extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'is_urgent',
        'is_premiere',
        'is_featured',
        'is_semi_new',
        'is_new',
        'is_available',
        'is_top',
    ];

    protected $casts = [
        'is_urgent'     => 'boolean',
        'is_premiere'   => 'boolean',
        'is_featured'   => 'boolean',
        'is_semi_new'   => 'boolean',
        'is_new'        => 'boolean',
        'is_available'  => 'boolean',
        'is_top'        => 'boolean',
    ];

    public function subcategories()
    {
        return $this->hasMany(AdSubcategory::class, 'ad_categories_id');
    }

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class, 'ad_categories_id');
    }
}
