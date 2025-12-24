<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdultContentPublishTerm extends Model
{
    protected $table = 'adult_content_publish_terms';

    protected $fillable = [
        'icon',
        'title',
        'description',
    ];
}
