<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashBox extends Model
{
    protected $fillable = [
        'user_id',
        'opening_balance',
        'current_balance',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movements()
    {
        return $this->hasMany(CashMovement::class);
    }
}
