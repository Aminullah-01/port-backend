<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'problem',
        'solution',
        'technologies',
        'category',
        'thumbnail',
        'featured',
        'status',
        'github_url',
        'live_url',
        'display_order',
    ];

    protected $casts = [
        'technologies' => 'array',
        'featured' => 'boolean',
        'display_order' => 'integer',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order', 'asc');
    }
}
