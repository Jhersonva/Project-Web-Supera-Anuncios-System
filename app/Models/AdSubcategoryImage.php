<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdSubcategoryImage extends Model
{
    protected $fillable = [
        'ad_subcategory_id',
        'image',
        'order'
    ];

    public function subcategory()
    {
        return $this->belongsTo(AdSubcategory::class);
    }
}