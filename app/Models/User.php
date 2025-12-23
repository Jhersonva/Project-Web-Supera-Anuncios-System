<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Advertisement;
use App\Models\Recharge;

class User extends Authenticatable
{
    protected $fillable = [
        'role_id',
        'profile_image',
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
        'privacy_policy_accepted',
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

    public function cashBox()
    {
        return $this->hasOne(CashBox::class, 'user_id');
    }

    public function cashMovements()
    {
        return $this->hasMany(CashMovement::class, 'employee_id');
    }

    public function conversationsSent()
    {
        return $this->hasMany(Conversation::class, 'sender_id');
    }

    public function conversationsReceived()
    {
        return $this->hasMany(Conversation::class, 'receiver_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
