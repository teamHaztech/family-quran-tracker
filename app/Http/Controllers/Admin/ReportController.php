<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\ExportService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reports)
    {
    }

    public function index(Request $request): View
    {
        $report = $this->reports->build($request->only('period', 'from', 'to', 'member_id'));
        $members = User::members()->orderBy('first_name')->get();

        return view('admin.reports.index', compact('report', 'members'));
    }

    public function export(Request $request, ExportService $export): Response
    {
        $format = $request->get('format', 'csv');
        $report = $this->reports->build($request->only('period', 'from', 'to', 'member_id'));

        ActivityLogger::log('report.exported', "Exported {$report['period']} report as {$format}");

        return match ($format) {
            'xlsx' => $export->xlsx($report),
            'pdf' => $export->pdf($report),
            default => $export->csv($report),
        };
    }
}
