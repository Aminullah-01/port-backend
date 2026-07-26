<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'icon',
        'description',
        'features',
        'display_order',
    ];

    protected $casts = [
        'features' => 'array',
        'display_order' => 'integer',
    ];
}
