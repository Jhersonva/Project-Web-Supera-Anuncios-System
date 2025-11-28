<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCategory extends Model
{
    protected $fillable = [
        'name',
        'icon',
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
