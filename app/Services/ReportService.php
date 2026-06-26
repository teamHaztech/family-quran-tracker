<?php

namespace App\Services;

use App\Models\ReadingSession;
use App\Models\User;
use Illuminate\Support\Carbon;

class ReportService
{
    /**
     * Resolve filters into a date range + optional member, then build the
     * report dataset (rows + summary + per-member breakdown).
     */
    public function build(array $filters): array
    {
        $period = $filters['period'] ?? 'monthly';
        [$from, $to] = $this->range($period, $filters['from'] ?? null, $filters['to'] ?? null);
        $memberId = $filters['member_id'] ?? null;

        $query = ReadingSession::query()
            ->with('user')
            ->whereBetween('date', [$from, $to])
            ->when($memberId, fn ($q) => $q->where('user_id', $memberId))
            ->orderBy('date');

        $rows = $query->get();

        $summary = [
            'total_pages' => (int) $rows->sum('pages_read'),
            'total_minutes' => (int) $rows->sum('minutes_read'),
            'total_sessions' => $rows->count(),
            'active_readers' => $rows->pluck('user_id')->unique()->count(),
        ];

        // Per-member breakdown
        $byMember = $rows->groupBy('user_id')->map(function ($group) {
            return [
                'name' => $group->first()->user?->fullName() ?? 'Unknown',
                'pages' => (int) $group->sum('pages_read'),
                'minutes' => (int) $group->sum('minutes_read'),
                'sessions' => $group->count(),
            ];
        })->sortByDesc('pages')->values();

        return [
            'period' => $period,
            'from' => Carbon::parse($from),
            'to' => Carbon::parse($to),
            'member' => $memberId ? User::find($memberId) : null,
            'rows' => $rows,
            'summary' => $summary,
            'by_member' => $byMember,
        ];
    }

    /**
     * @return array{0: \Illuminate\Support\Carbon, 1: \Illuminate\Support\Carbon}
     */
    protected function range(string $period, ?string $from, ?string $to): array
    {
        return match ($period) {
            'daily' => [today(), today()],
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            'yearly' => [now()->startOfYear(), now()->endOfYear()],
            'custom' => [
                $from ? Carbon::parse($from) : today()->subDays(30),
                $to ? Carbon::parse($to) : today(),
            ],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}
