<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1e293b; font-size: 11px; margin: 0; }
        .header { background: #047857; color: #fff; padding: 18px 24px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 4px 0 0; font-size: 11px; color: #d1fae5; }
        .wrap { padding: 18px 24px; }
        .summary { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .summary td { width: 25%; padding: 10px; background: #f1f5f9; border: 3px solid #fff; text-align: center; }
        .summary .num { font-size: 20px; font-weight: bold; color: #047857; }
        .summary .lbl { font-size: 9px; color: #64748b; text-transform: uppercase; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #ecfdf5; color: #065f46; text-align: left; padding: 7px 8px; font-size: 10px; border-bottom: 2px solid #a7f3d0; }
        table.data td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        table.data tr:nth-child(even) td { background: #f8fafc; }
        h3 { color: #047857; margin: 16px 0 8px; font-size: 13px; }
        .right { text-align: right; }
        .foot { margin-top: 18px; text-align: center; color: #94a3b8; font-size: 9px; }
    </style>
</head>
<body>
    @php $s = $report['summary']; @endphp
    <div class="header">
        <h1>☪ {{ \App\Models\Setting::current()->family_name }} — Quran Reading Report</h1>
        <p>
            {{ ucfirst($report['period']) }} report ·
            {{ $report['from']->format('M d, Y') }} – {{ $report['to']->format('M d, Y') }}
            @if ($report['member']) · Member: {{ $report['member']->fullName() }} @endif
            · Generated {{ now()->format('M d, Y H:i') }}
        </p>
    </div>

    <div class="wrap">
        <table class="summary">
            <tr>
                <td><div class="num">{{ $s['total_pages'] }}</div><div class="lbl">Total Pages</div></td>
                <td><div class="num">{{ $s['total_minutes'] }}</div><div class="lbl">Total Minutes</div></td>
                <td><div class="num">{{ $s['total_sessions'] }}</div><div class="lbl">Sessions</div></td>
                <td><div class="num">{{ $s['active_readers'] }}</div><div class="lbl">Active Readers</div></td>
            </tr>
        </table>

        @if ($report['by_member']->isNotEmpty())
            <h3>Summary by Member</h3>
            <table class="data">
                <thead><tr><th>Member</th><th class="right">Pages</th><th class="right">Minutes</th><th class="right">Sessions</th></tr></thead>
                <tbody>
                    @foreach ($report['by_member'] as $row)
                        <tr><td>{{ $row['name'] }}</td><td class="right">{{ $row['pages'] }}</td><td class="right">{{ $row['minutes'] }}</td><td class="right">{{ $row['sessions'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h3>Detailed Reading Sessions</h3>
        <table class="data">
            <thead><tr><th>Date</th><th>Member</th><th>Surah</th><th>Juz</th><th class="right">Pages</th><th class="right">Minutes</th></tr></thead>
            <tbody>
                @forelse ($report['rows'] as $row)
                    <tr>
                        <td>{{ $row->date->format('Y-m-d') }}</td>
                        <td>{{ $row->user?->fullName() }}</td>
                        <td>{{ $row->surah ?? '—' }}</td>
                        <td>{{ $row->juz ?? '—' }}</td>
                        <td class="right">{{ $row->pages_read }}</td>
                        <td class="right">{{ $row->minutes_read }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; color:#94a3b8;">No reading sessions in this period.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="foot">Family Quran Tracker · v{{ config('quran.version') }} · "The best of you are those who learn the Qur'an and teach it."</div>
    </div>
</body>
</html>
