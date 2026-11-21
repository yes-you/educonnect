<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMessage extends Model
{
    protected $fillable = ['study_group_id', 'user_id', 'body'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
