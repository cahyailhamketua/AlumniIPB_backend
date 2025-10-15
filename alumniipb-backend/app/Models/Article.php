<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal',
        'kategori',
        'isi_artikel',
        'image',
    ];

    public function usersWhoLiked()
    {
        return $this->belongsToMany(User::class, 'article_likes');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
