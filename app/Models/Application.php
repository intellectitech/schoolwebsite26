<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'date_of_birth', 'email', 'phone',
        'current_school', 'grade_applying_for', 'current_gpa', 'transcript_path',
        'personal_statement', 'status', 'draft_token', 'draft_saved_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'draft_saved_at' => 'datetime',
    ];

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }
}