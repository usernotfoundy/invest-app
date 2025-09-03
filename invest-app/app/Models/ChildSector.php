<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildSector extends Model
{
    protected $fillable = [
        'sector_id',
        'name',
        'data',
        'data_template',
        'file_path',
    ];

    protected $casts = [
        'data' => 'array',
        'data_template' => 'array',
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
