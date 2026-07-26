<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'organization',
        'issue_date',
        'credential_url',
        'image',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];
}
