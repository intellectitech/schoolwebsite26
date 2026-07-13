<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ContactDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'phone', 'email', 'is_emergency', 'sort_order',
    ];

    protected $casts = [
        'is_emergency' => 'boolean',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}