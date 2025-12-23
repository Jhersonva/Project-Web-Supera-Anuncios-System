<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdSubcategory extends Model
{
    protected $fillable = [
        'ad_categories_id',
        'name',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(AdCategory::class, 'ad_categories_id');
    }

    public function fields()
    {
        return $this->hasMany(FieldSubcategoryAd::class, 'ad_subcategories_id');
    }

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class, 'ad_subcategories_id');
    }

    public function images()
    {
        return $this->hasMany(AdSubcategoryImage::class);
    }

}
