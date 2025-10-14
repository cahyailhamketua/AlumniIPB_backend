<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Alumni extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'nama',
        'email',
        'nomor_telepon',
        'fakultas',
        'angkatan',
        'password',
    ];
}
