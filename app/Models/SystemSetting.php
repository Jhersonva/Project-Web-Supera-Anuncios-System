<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'company_name',
        'company_description',
        'logo',
    ];

    public function socialLinks()
    {
        return $this->hasMany(SystemSocialLink::class);
    }

}
