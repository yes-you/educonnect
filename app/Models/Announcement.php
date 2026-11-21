<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    public const AUDIENCES = ['all', 'students', 'parents'];

    protected $fillable = ['user_id', 'title', 'body', 'audience'];

    // Only the announcements meant for this user's role. Teachers and admin see every audience.
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $audiences = match (true) {
            $user->hasRole('student') => ['all', 'students'],
            $user->hasRole('parent') => ['all', 'parents'],
            $user->hasRole('organization') => ['all'],
            default => self::AUDIENCES,
        };

        return $query->whereIn('audience', $audiences);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
