<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'nombre',
        'tipo',
        'numero',
        'cuenta',
        'cci',
        'qr',
        'activo',
    ];

    public function recharges()
    {
        return $this->hasMany(Recharge::class);
    }
}
