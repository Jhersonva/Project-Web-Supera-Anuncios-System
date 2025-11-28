<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    /* Nombre de tabla personalizado */
    protected $table = 'advertisementss';

    /* Campos asignables */
    protected $fillable = [
        'ad_categories_id',
        'ad_subcategories_id',
        'user_id',
        'title',
        'description',
        'amount',
        'days_active',
        'expires_at',
        'published',
        'stars',
        'urgent_publication',
        'status',
    ];

    /* Casts automáticos */
    protected $casts = [
        'price'               => 'decimal:2',
        'published'           => 'boolean',
        'urgent_publication'  => 'boolean',
        'stars'               => 'integer',
        'days_active'         => 'integer',
        'expires_at'          => 'datetime',
    ];

    /* Relaciones */

    /* Categoría principal */
    public function category()
    {
        return $this->belongsTo(AdCategory::class, 'ad_categories_id', 'id');
    }

    /* Subcategoría */
    public function subcategory()
    {
        return $this->belongsTo(AdSubcategory::class, 'ad_subcategories_id', 'id');
    }

    /* Campos dinámicos -> valores */
    public function fields_values()
    {
        return $this->hasMany(ValueFieldAd::class, 'advertisementss_id', 'id');
    }

    /* Usuario dueño del anuncio */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
}
