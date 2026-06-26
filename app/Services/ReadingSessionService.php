<?php

namespace App\Services;

use App\Models\ReadingSession;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReadingSessionService
{
    public function __construct(protected BadgeService $badges)
    {
    }

    /**
     * Create a reading session, computing derived fields, logging activity and
     * evaluating badges.
     *
     * @return array{session: ReadingSession, newBadges: Collection}
     */
    public function create(User $user, array $data): array
    {
        $data = $this->normalize($data);

        $session = $user->readingSessions()->create($data);

        ActivityLogger::log(
            $data['method'] === 'timer' ? 'reading.finished' : 'reading.created',
            "Logged {$session->pages_read} page(s) in {$session->minutes_read} min",
            ['session_id' => $session->id],
            $user->id,
        );

        $newBadges = $this->badges->evaluate($user);

        return ['session' => $session, 'newBadges' => $newBadges];
    }

    /**
     * Update an existing reading session.
     */
    public function update(ReadingSession $session, array $data): ReadingSession
    {
        $data = $this->normalize($data, $session);
        $session->update($data);

        ActivityLogger::log(
            'reading.edited',
            "Edited reading session #{$session->id}",
            ['session_id' => $session->id],
            $session->user_id,
        );

        return $session;
    }

    /**
     * Normalise incoming data: compute pages_read from page range, minutes from
     * timer timestamps, and default the date/method.
     */
    protected function normalize(array $data, ?ReadingSession $existing = null): array
    {
        $data['date'] = $data['date'] ?? optional($existing)->date?->toDateString() ?? today()->toDateString();
        $data['method'] = $data['method'] ?? $existing->method ?? 'manual';

        // Compute pages read from the page range when both are present.
        $start = $data['start_page'] ?? $existing->start_page ?? null;
        $end = $data['end_page'] ?? $existing->end_page ?? null;

        if (! isset($data['pages_read']) || $data['pages_read'] === null) {
            if ($start !== null && $end !== null && $end >= $start) {
                $data['pages_read'] = ($end - $start) + 1;
            } else {
                $data['pages_read'] = $data['pages_read'] ?? 0;
            }
        }

        // Compute minutes from timer timestamps when provided.
        if (! empty($data['started_at']) && ! empty($data['ended_at'])) {
            $started = Carbon::parse($data['started_at']);
            $ended = Carbon::parse($data['ended_at']);
            $data['minutes_read'] = max(1, (int) round($started->diffInSeconds($ended) / 60));
        }

        $data['minutes_read'] = $data['minutes_read'] ?? $existing->minutes_read ?? 0;

        return $data;
    }
}
