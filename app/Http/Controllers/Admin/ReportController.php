<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\PaymentTerm;
use App\Models\Project;
use App\Models\User;
use App\Services\AdminAuditLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ], [
            'to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);
        [$from, $to] = $this->dateRange($validated);

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'reports' => $this->reports($from, $to),
        ]);
    }

    public function export(
        Request $request,
        string $report,
        string $format,
        AdminAuditLogger $auditLogger,
    ): Response {
        abort_unless(in_array($report, ['konsultasi', 'proyek', 'pembayaran'], true), 404);
        abort_unless(in_array($format, ['csv', 'xlsx'], true), 404);
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ], [
            'to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);
        [$from, $to] = $this->dateRange($validated);
        $rows = $this->reports($from, $to)[$report];
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        $auditLogger->record(
            $admin,
            'laporan_diekspor',
            $report,
            null,
            null,
            ['format' => $format, 'from' => $from, 'to' => $to, 'baris' => count($rows) - 1],
        );

        $filename = "laporan-{$report}-{$from}-{$to}.{$format}";
        if ($format === 'csv') {
            return response()->streamDownload(function () use ($rows): void {
                $output = fopen('php://output', 'w');
                if ($output === false) {
                    throw new \RuntimeException('Tidak dapat membuka aliran ekspor CSV.');
                }
                fwrite($output, "\xEF\xBB\xBF");
                foreach ($rows as $row) {
                    fputcsv($output, array_map($this->safeCsvCell(...), $row), ',', '"', '');
                }
                fclose($output);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        return response($this->makeXlsx($rows), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * @param  array{from?: string, to?: string}  $validated
     * @return array{string, string}
     */
    private function dateRange(array $validated): array
    {
        $from = $validated['from'] ?? now()->startOfMonth()->toDateString();
        $to = $validated['to'] ?? now()->endOfMonth()->toDateString();

        return [$from, $to];
    }

    /**
     * @return array<string, list<list<string|int>>>
     */
    private function reports(string $from, string $to): array
    {
        $consultations = Consultation::query()
            ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->orderBy('id_konsultasi')
            ->get();
        $projects = Project::query()
            ->with('client:id,name')
            ->whereBetween('tgl_mulai', [$from, $to])
            ->orderBy('id_proyek')
            ->get();
        $payments = PaymentTerm::query()
            ->with('project:id_proyek,nomor_proyek,nama')
            ->whereBetween('jatuh_tempo', [$from, $to])
            ->orderBy('id_proyek')
            ->orderBy('urutan')
            ->get();

        return [
            'konsultasi' => [
                ['ID', 'Nama', 'Layanan', 'Rentang budget', 'Status', 'Tanggal'],
                ...$consultations->map(fn (Consultation $item): array => [
                    $item->nomor_konsultasi,
                    $item->nama,
                    $item->layanan,
                    $item->rentang_budget ?? '',
                    $item->status->value,
                    $item->created_at->toDateString(),
                ])->all(),
            ],
            'proyek' => [
                ['ID', 'Proyek', 'Klien', 'Tanggal', 'Status', 'Total'],
                ...$projects->map(fn (Project $item): array => [
                    $item->nomor_proyek,
                    $item->nama,
                    $item->client?->name ?? '',
                    $item->tgl_mulai->toDateString(),
                    $item->status,
                    $item->total_nilai,
                ])->all(),
            ],
            'pembayaran' => [
                ['Proyek', 'Termin', 'Jumlah', 'Status', 'Tanggal bayar'],
                ...$payments->map(fn (PaymentTerm $item): array => [
                    $item->project?->nomor_proyek ?? '',
                    $item->nama,
                    $item->nominal,
                    $item->status,
                    $item->tgl_bayar?->toDateString() ?? '',
                ])->all(),
            ],
        ];
    }

    private function safeCsvCell(string|int $value): string|int
    {
        if (is_string($value) && preg_match('/^[\s]*[=+\-@]/', $value) === 1) {
            return "'{$value}";
        }

        return $value;
    }

    /**
     * @param  list<list<string|int>>  $rows
     */
    private function makeXlsx(array $rows): string
    {
        $sheetRows = [];
        foreach ($rows as $rowIndex => $row) {
            $cells = [];
            foreach ($row as $columnIndex => $value) {
                $reference = $this->columnName($columnIndex + 1).($rowIndex + 1);
                if (is_int($value)) {
                    $cells[] = '<c r="'.$reference.'"><v>'.$value.'</v></c>';
                } else {
                    $text = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                    $cells[] = '<c r="'.$reference.'" t="inlineStr"><is><t xml:space="preserve">'.$text.'</t></is></c>';
                }
            }
            $sheetRows[] = '<row r="'.($rowIndex + 1).'">'.implode('', $cells).'</row>';
        }

        $lastColumn = $this->columnName(count($rows[0]));
        $lastRow = count($rows);
        $files = [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Laporan" sheetId="1" r:id="rId1"/></sheets></workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>',
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><dimension ref="A1:'.$lastColumn.$lastRow.'"/><sheetData>'.implode('', $sheetRows).'</sheetData></worksheet>',
        ];

        $temporaryFile = tempnam(sys_get_temp_dir(), 'empirise-report-');
        if ($temporaryFile === false) {
            throw new \RuntimeException('Tidak dapat menyiapkan file ekspor XLSX.');
        }
        $archive = new \ZipArchive;
        if ($archive->open($temporaryFile, \ZipArchive::OVERWRITE) !== true) {
            @unlink($temporaryFile);
            throw new \RuntimeException('Tidak dapat membuat file XLSX.');
        }
        foreach ($files as $path => $content) {
            $archive->addFromString($path, $content);
        }
        $archive->close();
        $contents = file_get_contents($temporaryFile);
        @unlink($temporaryFile);
        if ($contents === false) {
            throw new \RuntimeException('Tidak dapat membaca file XLSX yang telah dibuat.');
        }

        return $contents;
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr($number % 26 + 65).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }
}
