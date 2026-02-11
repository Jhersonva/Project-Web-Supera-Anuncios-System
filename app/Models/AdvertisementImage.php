<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdvertisementImage extends Model
{
    protected $fillable = [
        'advertisementss_id',
        'uid',
        'image',
        'is_main',
        'crop_data'
    ];

    protected $casts = [
        'crop_data' => 'array'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uid)) {
                $model->uid = (string) Str::uuid();
            }
        });
    }

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class, 'advertisementss_id');
    }
}
