<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    protected $fillable = [
        'user_id',
        'monto',
        'metodo_pago',
        'img_cap_pago',
        'operation_number',
        'reject_message',
        'status',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
