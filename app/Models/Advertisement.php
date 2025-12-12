<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Advertisement extends Model
{
    protected $table = 'advertisementss';

    protected $fillable = [
        'ad_categories_id',
        'ad_subcategories_id',
        'user_id',
        'title',
        'description',
        'contact_location',
        'amount',
        'amount_visible',
        'days_active',
        'expires_at',
        'published',
        'stars',
        'urgent_publication',
        'urgent_price',
        'featured_publication',
        'featured_price',
        'premiere_publication',
        'premiere_price',
        'status',
        'receipt_type',
        'dni',
        'full_name',
        'ruc',
        'company_name',
        'address',
    ];

    protected $casts = [
        'amount'                => 'decimal:2',
        'published'             => 'boolean',
        'urgent_publication'    => 'boolean',
        'urgent_price'          => 'decimal:2',
        'featured_publication'  => 'boolean',
        'featured_price'        => 'decimal:2',
        'stars'                 => 'integer',
        'days_active'           => 'integer',
        'expires_at'            => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(AdCategory::class, 'ad_categories_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(AdSubcategory::class, 'ad_subcategories_id');
    }

    public function fields_values()
    {
        return $this->hasMany(ValueFieldAd::class, 'advertisementss_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(AdvertisementImage::class, 'advertisementss_id');
    }

    public function mainImage()
    {
        return $this->hasOne(AdvertisementImage::class, 'advertisementss_id')
                    ->where('is_main', true);
    }

    public function getSlugAttribute()
    {
        return Str::slug($this->title);
    }

    public function getDetailUrlAttribute()
    {
        return url("/detalle-anuncio/{$this->slug}/{$this->id}");
    }
}
