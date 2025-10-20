<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAlumni()
    {
        return $this->role === 'alumni';
    }

    public function alumni()
    {
        return $this->hasOne(Alumni::class);
    }

    // Add a virtual attribute for name from the related alumni model
    public function getNameAttribute()
    {
        return $this->alumni->nama ?? null;
    }

    public function likedArticles()
    {
        return $this->belongsToMany(Article::class, 'article_likes');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedGalleries()
    {
        return $this->belongsToMany(Gallery::class, 'gallery_likes');
    }

    public function galleryComments()
    {
        return $this->hasMany(GalleryComment::class);
    }
}
