<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InProfile extends Model
{
    protected $fillable = [
        'profile_name',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
