<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class, 'assigned_sector');
    }

    public function children()
    {
        return $this->hasMany(ChildSector::class, 'sector_id');
    }
}
