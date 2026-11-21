<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    public const TYPES = ['module', 'study_guide'];

    protected $fillable = [
        'teacher_id', 'title', 'subject', 'grade_level', 'type',
        'description', 'file_path', 'original_name',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }
}
