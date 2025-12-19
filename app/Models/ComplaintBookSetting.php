<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintBookSetting extends Model
{
    protected $fillable = [
        'business_name',
        'ruc',
        'address',
        'legal_text',
        'notification_email',
    ];
}
