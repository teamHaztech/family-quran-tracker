<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    protected array $headings = ['Date', 'Member', 'Surah', 'Juz', 'Start Page', 'End Page', 'Pages Read', 'Minutes', 'Method', 'Notes'];

    /** Map a report into table rows of scalar values. */
    protected function dataRows(array $report): array
    {
        return $report['rows']->map(fn ($s) => [
            $s->date->format('Y-m-d'),
            $s->user?->fullName() ?? '—',
            $s->surah ?? '—',
            $s->juz ?? '',
            $s->start_page ?? '',
            $s->end_page ?? '',
            $s->pages_read,
            $s->minutes_read,
            $s->method,
            $s->notes ?? '',
        ])->all();
    }

    public function filename(array $report, string $ext): string
    {
        $scope = $report['member']?->fullName() ?? 'family';
        return 'quran-report-' . str($scope)->slug() . '-' . $report['from']->format('Ymd') . '-' . $report['to']->format('Ymd') . '.' . $ext;
    }

    /** CSV via a streamed response (no extra dependency). */
    public function csv(array $report): StreamedResponse
    {
        $filename = $this->filename($report, 'csv');
        $headings = $this->headings;
        $rows = $this->dataRows($report);

        return response()->streamDownload(function () use ($headings, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headings);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /** XLSX via OpenSpout (PHP 8.5-compatible). */
    public function xlsx(array $report): Response
    {
        $filename = $this->filename($report, 'xlsx');
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');

        $writer = new XlsxWriter();
        $writer->openToFile($tmp);
        $writer->addRow(Row::fromValues($this->headings));
        foreach ($this->dataRows($report) as $row) {
            $writer->addRow(Row::fromValues($row));
        }
        $writer->close();

        return response()->download($tmp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /** PDF via DomPDF. */
    public function pdf(array $report): Response
    {
        $filename = $this->filename($report, 'pdf');

        return Pdf::loadView('admin.reports.pdf', ['report' => $report])
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }
}
