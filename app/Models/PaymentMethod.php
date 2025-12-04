<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name_method',
        'type',
        'logo',
        'holder_name',
        'cell_phone_number',
        'account_number',
        'cci',
        'qr',
        'active',
    ];

    public function recharges()
    {
        return $this->hasMany(Recharge::class);
    }
}
