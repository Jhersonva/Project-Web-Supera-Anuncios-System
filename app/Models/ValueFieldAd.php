<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValueFieldAd extends Model
{
    protected $table = 'values_fields_ads';

    protected $fillable = [
        'advertisementss_id',
        'fields_subcategory_ads_id',
        'value',
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class, 'advertisementss_id');
    }

    public function field()
    {
        return $this->belongsTo(FieldSubcategoryAd::class, 'fields_subcategory_ads_id');
    }
}
