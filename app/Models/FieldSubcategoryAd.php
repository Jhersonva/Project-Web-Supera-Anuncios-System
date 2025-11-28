<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldSubcategoryAd extends Model
{
    protected $table = 'fields_subcategory_ads';

    protected $fillable = [
        'ad_subcategories_id',
        'name',
        'type',
    ];

    public function subcategory()
    {
        return $this->belongsTo(AdSubcategory::class, 'ad_subcategories_id');
    }

    public function values()
    {
        return $this->hasMany(ValueFieldAd::class, 'fields_subcategory_ads_id');
    }
}
