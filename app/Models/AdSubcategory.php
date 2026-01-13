<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdSubcategory extends Model
{
    protected $fillable = [
        'ad_categories_id',
        'name',
        'price',
        'is_urgent',
        'is_premiere',
        'is_featured',
        'is_semi_new',
        'is_new',
        'is_available',
        'is_top',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_urgent'     => 'boolean',
        'is_premiere'   => 'boolean',
        'is_featured'   => 'boolean',
        'is_semi_new'   => 'boolean',
        'is_new'        => 'boolean',
        'is_available'  => 'boolean',
        'is_top'        => 'boolean',
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
