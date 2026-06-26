<?php

namespace App\Repositories;

use App\Models\ReadingSession;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class ReadingSessionRepository
{
    /**
     * Paginated, filterable history for a member.
     */
    public function historyFor(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = $user->readingSessions()->getQuery()->latest('date')->latest('id');

        if (! empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('surah', 'like', $term)
                    ->orWhere('notes', 'like', $term);
            });
        }

        if (! empty($filters['from'])) {
            $query->whereDate('date', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('date', '<=', $filters['to']);
        }

        if (! empty($filters['period'])) {
            [$from, $to] = $this->periodRange($filters['period']);
            $query->whereBetween('date', [$from, $to]);
        }

        return $query->paginate(15)->withQueryString();
    }

    /**
     * Today's session for a member, if any (used for "edit today").
     */
    public function todayFor(User $user): ?ReadingSession
    {
        return $user->readingSessions()->whereDate('date', today())->latest('id')->first();
    }

    /**
     * Resolve a named period into a [from, to] date range.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function periodRange(string $period): array
    {
        return match ($period) {
            'daily' => [today(), today()],
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            'yearly' => [now()->startOfYear(), now()->endOfYear()],
            default => [today()->subDays(30), today()],
        };
    }
}
