<?php

use App\Enums\UserRole;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use ZipArchive;

uses(LazilyRefreshDatabase::class);

it('exports CSV with formula-like values neutralized and records the export', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Consultation::factory()->create([
        'nama' => '=HYPERLINK("https://example.test")',
        'created_at' => '2026-11-20 12:00:00',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.reports.export', [
            'report' => 'konsultasi',
            'format' => 'csv',
            'from' => '2026-11-01',
            'to' => '2026-11-30',
        ]))
        ->assertOk()
        ->assertDownload('laporan-konsultasi-2026-11-01-2026-11-30.csv');

    expect($response->streamedContent())
        ->toContain("'=HYPERLINK")
        ->toContain('Rentang budget');
    $this->assertDatabaseHas('audit_log', [
        'id_user' => $admin->id,
        'aksi' => 'laporan_diekspor',
        'objek' => 'konsultasi',
    ]);
});

it('exports a valid XLSX workbook with formula-like values stored as text', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Consultation::factory()->create([
        'nama' => '=HYPERLINK("https://example.test")',
        'created_at' => '2026-11-20 12:00:00',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.reports.export', [
            'report' => 'konsultasi',
            'format' => 'xlsx',
            'from' => '2026-11-01',
            'to' => '2026-11-30',
        ]))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->assertDownload('laporan-konsultasi-2026-11-01-2026-11-30.xlsx');

    $temporaryFile = tempnam(sys_get_temp_dir(), 'empirise-test-report-');
    expect($temporaryFile)->not->toBeFalse();
    file_put_contents($temporaryFile, $response->getContent());

    $archive = new ZipArchive;
    expect($archive->open($temporaryFile))->toBeTrue();
    $sheet = $archive->getFromName('xl/worksheets/sheet1.xml');
    $archive->close();
    unlink($temporaryFile);

    expect($sheet)
        ->toContain('t="inlineStr"')
        ->toContain('=HYPERLINK')
        ->not->toContain('<f>');
});

it('forbids clients from exporting admin reports', function () {
    $client = User::factory()->create(['role' => UserRole::Klien]);

    $this->actingAs($client)
        ->get(route('admin.reports.export', [
            'report' => 'konsultasi',
            'format' => 'csv',
        ]))
        ->assertForbidden();
});
