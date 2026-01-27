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
        'account_type',
        'full_name',
        'dni',
        'company_reason',
        'ruc',
        'password',
        'email',
        'phone',
        'locality',
        'whatsapp',
        'call_phone',
        'contact_email',
        'address',
        'birthdate',
        'privacy_policy_accepted',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'date',
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

    public function getIsProfileCompleteAttribute(): bool
    {
        // Campos comunes obligatorios
        $common = [
            $this->email,
            $this->locality,
            $this->whatsapp,
            $this->call_phone,
        ];

        foreach ($common as $value) {
            if (empty($value)) {
                return false;
            }
        }

        // Persona natural
        if ($this->account_type === 'person') {
            return !empty($this->full_name)
                && !empty($this->dni);
        }

        // Empresa
        if ($this->account_type === 'business') {
            return !empty($this->company_reason)
                && !empty($this->ruc);
        }

        return false;
    }

}
