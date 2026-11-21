<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = ['admin', 'teacher', 'student', 'parent', 'organization'];

    public const SELF_REGISTER_ROLES = ['student', 'parent', 'organization'];

    // Roles only an admin can create.
    public const STAFF_ROLES = ['teacher', 'admin'];

    public const GRADE_LEVELS = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'grade_level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    // A parent's children (users with role student).
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id');
    }

    // Quiz progress: attempts, average percentage and best score for each quiz, plus overall totals.
    public function quizProgress(): array
    {
        $attempts = $this->quizAttempts()->with('quiz')->get();

        $quizzes = $attempts->groupBy('quiz_id')->map(fn ($group) => [
            'quiz' => $group->first()->quiz,
            'attempts' => $group->count(),
            'average' => (int) round($group->avg(fn ($attempt) => $attempt->percentage()) ?? 0),
            'best' => $group->sortByDesc(fn ($attempt) => $attempt->percentage())->first(),
        ]);

        return [
            'attempts' => $attempts->count(),
            'average' => (int) round($attempts->avg(fn ($attempt) => $attempt->percentage()) ?? 0),
            'quizzes' => $quizzes,
        ];
    }

    // A student's parents.
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'teacher_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'teacher_id');
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    public function studyGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudyGroup::class)->withTimestamps();
    }

    public function ownedStudyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'owner_id');
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }
}
