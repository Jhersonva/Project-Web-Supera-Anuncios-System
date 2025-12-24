<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSocialLink extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'url',
        'is_active',
        'order',
    ];
}
