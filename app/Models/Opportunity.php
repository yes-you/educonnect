<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opportunity extends Model
{
    use HasFactory;

    public const TYPES = ['scholarship', 'tutoring', 'event'];

    public const STATUSES = ['pending', 'approved', 'rejected'];

    protected $fillable = [
        'user_id', 'type', 'title', 'description', 'location',
        'event_date', 'deadline', 'link', 'status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'deadline' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }
}
