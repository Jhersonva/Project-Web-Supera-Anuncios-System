<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementImage extends Model
{
    protected $fillable = [
        'advertisementss_id',
        'image',
        'is_main'
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class, 'advertisementss_id');
    }
}
