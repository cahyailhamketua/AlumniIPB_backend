<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Alumni extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'nama',
        'nomor_telepon',
        'fakultas',
        'angkatan',
        'user_id',
        'image',
        'pekerjaan',
        'perusahaan',
        'alamat',
        'biografi',
        'riwayat_pekerjaan',
    ];

    protected $casts = [
        'riwayat_pekerjaan' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
