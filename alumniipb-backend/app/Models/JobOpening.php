<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOpening extends Model
{
    use HasFactory;

    protected $table = 'job_openings';

    protected $fillable = [
        'position',
        'description',
        'industry',
        'company',
        'type',
        'deadline',
        'location',
        'image',
        'salary_min',
        'salary_max',
        'requirements',
        'link',
        'active',
        'created_by_id',
        'created_by_type',
        'approved_by_id',
        'approved_at',
    ];

    protected $casts = [
        'requirements' => 'array',
        'deadline' => 'datetime',
        'active' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
