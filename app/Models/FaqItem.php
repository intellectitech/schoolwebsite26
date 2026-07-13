<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FaqItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'question', 'answer', 'category', 'sort_order',
    ];

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category)->orderBy('sort_order');
    }
}