<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivacyPolicySetting extends Model
{
    protected $fillable = [
        'privacy_text',
        'contains_explicit_content',
        'requires_adult',
        'is_active',
    ];

    protected $casts = [
        'contains_explicit_content' => 'boolean',
        'requires_adult' => 'boolean',
        'is_active' => 'boolean',
    ];
}
