<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'module_id', 'title', 'subject', 'grade_level',
        'description', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Published quizzes of the student's own grade level (the only ones a student may open).
    public function scopeOpenTo(Builder $query, User $user): Builder
    {
        return $query->where('is_published', true)->where('grade_level', $user->grade_level);
    }

    // The user's one attempt at this quiz, or null if they have not taken it yet.
    public function attemptBy(User $user): ?QuizAttempt
    {
        return $this->attempts()->where('user_id', $user->id)->first();
    }
}
