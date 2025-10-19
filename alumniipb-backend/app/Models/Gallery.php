<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'judul_galery',
        'deskripsi',
        'tanggal',
        'kategori',
        'jumlah_peserta',
        'foto_kegiatan',
        'lokasi',
    ];

    public function usersWhoLiked()
    {
        return $this->belongsToMany(User::class, 'gallery_likes');
    }

    public function comments()
    {
        return $this->hasMany(GalleryComment::class);
    }
}
