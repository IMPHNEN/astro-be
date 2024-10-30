<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class project extends Model
{
    protected $fillable = [
        'title',
        'submiter',
        'description',
        'proposal',
        'status',
        'slug',
        'category',
        'expected_price',
    ];


}
