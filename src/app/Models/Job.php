<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company',
        'location',
        'posting_date',
        'link',
        'logo_url',
        'unique_hash',
    ];

    protected $casts = [
            'posting_date' => 'date',
    ];
}