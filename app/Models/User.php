<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Advertisement;
use App\Models\Recharge;

class User extends Authenticatable
{
    protected $fillable = [
        'role_id',
        'full_name',
        'password',
        'email',
        'dni',
        'phone',
        'locality',
        'whatsapp',
        'call_phone',
        'contact_email',
        'address',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* Relaciones */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function recharges()
    {
        return $this->hasMany(Recharge::class);
    }
}
