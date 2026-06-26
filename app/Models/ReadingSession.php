<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'surah',
        'start_page',
        'end_page',
        'pages_read',
        'minutes_read',
        'juz',
        'notes',
        'started_at',
        'ended_at',
        'method',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }
}
