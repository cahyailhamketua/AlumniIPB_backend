<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal',
        'like',
        'komentar',
        'kategori',
        'isi_artikel',
        'image',
    ];
}
