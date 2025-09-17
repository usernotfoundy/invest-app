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
        'powerbi_path',
    ];

    protected $appends = ['api_url'];

    public function getApiUrlAttribute()
    {
        return config('app.url') . '/api/get-data/' . $this->id;
    }

    protected $casts = [
        'data' => 'array',
        'data_template' => 'array',
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
