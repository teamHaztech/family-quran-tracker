<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'daily_goal_pages',
        'current_surah',
        'current_ayah',
        'theme',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
