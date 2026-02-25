<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Advertisement extends Model
{
    protected $table = 'advertisementss';
    protected $appends = ['time_ago'];  

    protected $fillable = [
        'ad_categories_id',
        'ad_subcategories_id',
        'user_id',
        'title',
        'description',
        'contact_location',
        'department',
        'province',
        'district',
        'whatsapp',
        'call_phone',
        'amount',
        'amount_currency',
        'amount_visible',
        'amount_text',
        'days_active',
        'featured_days',
        'expires_at',
        'published',
        'published_at',
        'stars',
        'urgent_publication',
        'urgent_price',
        'featured_publication',
        'featured_price',
        'featured_started_at',
        'featured_expires_at',
        'premiere_publication',
        'premiere_price',
        'semi_new_publication',
        'semi_new_price',
        'new_publication',
        'new_price',
        'available_publication',
        'available_price',
        'top_publication',
        'top_price',
        'verification_requested',
        'is_verified',
        'verified_at',
        'status',
        'refunded',
        'receipt_type',
        'dni',
        'full_name',
        'ruc',
        'company_name',
        'address',
        'receipt_file',
        'receipt_code',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'published' => 'boolean',
        'verification_requested' => 'boolean',
        'refunded' => 'boolean',
        'is_verified' => 'boolean',

        'urgent_publication' => 'boolean',
        'urgent_price' => 'decimal:2',

        'featured_publication' => 'boolean',
        'featured_price' => 'decimal:2',

        'featured_started_at' => 'datetime',
        'featured_expires_at' => 'datetime',

        'premiere_publication' => 'boolean',
        'premiere_price' => 'decimal:2',

        'semi_new_publication' => 'boolean',
        'semi_new_price' => 'decimal:2',

        'new_publication' => 'boolean',
        'new_price' => 'decimal:2',

        'available_publication' => 'boolean',
        'available_price' => 'decimal:2',

        'top_publication' => 'boolean',
        'top_price' => 'decimal:2',

        'stars' => 'integer',
        'days_active' => 'integer',
        'featured_days' => 'integer',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
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

    public function dynamicFields()
    {
        return $this->hasMany(ValueFieldAd::class,'advertisementss_id')->with('field'); 
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

    public function getTimeAgoAttribute()
    {
        if (!$this->published_at) {
            return 'Pendiente de aprobación';
        }

        return $this->published_at
            ->locale('es')
            ->diffForHumans();
    }
}
