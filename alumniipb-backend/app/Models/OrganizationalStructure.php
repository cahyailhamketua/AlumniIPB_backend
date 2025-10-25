<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationalStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'about_us_id',
        'name',
        'position',
        'tenure',
        'image',
    ];

    public function aboutUs()
    {
        return $this->belongsTo(AboutUs::class);
    }
}
