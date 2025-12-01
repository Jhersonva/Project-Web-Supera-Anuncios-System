<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    protected $fillable = [
        'cash_box_id',
        'employee_id',
        'type',
        'amount',
        'description'
    ];

    public function cashBox()
    {
        return $this->belongsTo(CashBox::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
