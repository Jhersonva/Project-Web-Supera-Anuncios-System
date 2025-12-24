<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdultContentViewTerm extends Model
{
    protected $table = 'adult_content_view_terms';

    protected $fillable = [
        'icon',
        'title',
        'description',
    ];
}
