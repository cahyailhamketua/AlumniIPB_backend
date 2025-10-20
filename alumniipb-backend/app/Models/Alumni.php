<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Alumni extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'nama',
        'nomor_telepon',
        'fakultas',
        'angkatan',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
