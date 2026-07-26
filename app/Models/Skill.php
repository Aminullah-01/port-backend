<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'category',
        'percentage',
        'color',
        'display_order',
    ];

    protected $casts = [
        'percentage' => 'integer',
        'display_order' => 'integer',
    ];
}
