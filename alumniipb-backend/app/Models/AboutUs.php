<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'history',
        'mission_focus',
        'contact',
        'address',
        'gmail',
        'instagram',
        'youtube',
    ];

    protected $casts = [
    ];

    public function organizationalStructures()
    {
        return $this->hasMany(OrganizationalStructure::class);
    }
}
