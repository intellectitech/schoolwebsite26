<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FacultyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'title', 'bio', 'photo_url', 'is_spotlighted', 'sort_order',
    ];

    protected $casts = [
        'is_spotlighted' => 'boolean',
    ];

    public function scopeSpotlighted(Builder $query): Builder
    {
        return $query->where('is_spotlighted', true)->orderBy('sort_order');
    }
}