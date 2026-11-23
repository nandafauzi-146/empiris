<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\PaymentTerm;
use App\Models\Project;
use App\Models\User;
use App\Services\AdminAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('admin.projects.index', [
            'projects' => Project::query()
                ->with(['client:id,name', 'terms'])
                ->latest('tgl_mulai')
                ->latest('id_proyek')
                ->paginate(12),
            'consultations' => Consultation::query()
                ->whereNotNull('id_klien')
                ->whereDoesntHave('project')
                ->orderBy('created_at')
                ->get(['id_konsultasi', 'nomor_konsultasi', 'nama', 'layanan']),
        ]);
    }

    public function store(Request $request, AdminAuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'id_konsultasi' => [
                'required',
                'integer',
                Rule::exists('konsultasi', 'id_konsultasi')->whereNotNull('id_klien'),
                Rule::unique('proyek', 'id_konsultasi'),
            ],
            'nama' => ['required', 'string', 'max:190'],
            'tgl_mulai' => ['required', 'date'],
            'tgl_selesai' => ['required', 'date', 'after_or_equal:tgl_mulai'],
            'total_nilai' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'catatan' => ['nullable', 'string', 'max:5000'],
            'jatuh_tempo' => ['nullable', 'array', 'size:3'],
            'jatuh_tempo.*' => ['nullable', 'date'],
        ], [
            'id_konsultasi.required' => 'Konsultasi wajib dipilih.',
            'id_konsultasi.exists' => 'Konsultasi tidak ditemukan atau belum tertaut ke akun klien.',
            'id_konsultasi.unique' => 'Konsultasi ini sudah memiliki proyek.',
            'nama.required' => 'Nama proyek wajib diisi.',
            'tgl_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'total_nilai.required' => 'Total proyek wajib diisi.',
            'total_nilai.min' => 'Total proyek harus lebih dari nol.',
        ]);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);

        $project = DB::transaction(function () use ($validated, $auditLogger, $admin): Project {
            $consultation = Consultation::query()
                ->whereNotNull('id_klien')
                ->findOrFail($validated['id_konsultasi']);

            $project = Project::query()->create([
                'id_konsultasi' => $consultation->id_konsultasi,
                'id_klien' => $consultation->id_klien,
                'nama' => $validated['nama'],
                'tgl_mulai' => $validated['tgl_mulai'],
                'tgl_selesai' => $validated['tgl_selesai'],
                'status' => 'terjadwal',
                'total_nilai' => $validated['total_nilai'],
                'catatan' => $validated['catatan'] ?? null,
            ]);
            $project->update([
                'nomor_proyek' => 'PR-'.str_pad((string) $project->id_proyek, 4, '0', STR_PAD_LEFT),
            ]);

            $firstTerm = intdiv($project->total_nilai * 30, 100);
            $secondTerm = intdiv($project->total_nilai * 40, 100);
            $amounts = [$firstTerm, $secondTerm, $project->total_nilai - $firstTerm - $secondTerm];
            $names = ['Termin 1 - Uang muka 30%', 'Termin 2 - 40%', 'Termin 3 - Pelunasan 30%'];

            foreach ($amounts as $index => $amount) {
                $project->terms()->create([
                    'urutan' => $index + 1,
                    'nama' => $names[$index],
                    'nominal' => $amount,
                    'jatuh_tempo' => $validated['jatuh_tempo'][$index] ?? $validated['tgl_mulai'],
                    'status' => 'belum_bayar',
                ]);
            }

            $auditLogger->record(
                $admin,
                'proyek_dibuat',
                'proyek',
                (int) $project->id_proyek,
                null,
                ['nomor_proyek' => $project->nomor_proyek, 'total_nilai' => $project->total_nilai],
            );

            return $project;
        });

        return to_route('admin.projects.index')
            ->with('status', "Proyek {$project->nomor_proyek} dan tiga termin pembayaran berhasil dibuat.");
    }

    public function updateStatus(
        Request $request,
        Project $project,
        AdminAuditLogger $auditLogger,
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['terjadwal', 'berjalan', 'selesai', 'batal'])],
        ], [
            'status.in' => 'Status proyek tidak valid.',
        ]);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);

        $previousStatus = $project->status;
        DB::transaction(function () use ($admin, $auditLogger, $project, $previousStatus, $validated): void {
            $project->update(['status' => $validated['status']]);
            $auditLogger->record(
                $admin,
                'status_proyek_diubah',
                'proyek',
                (int) $project->id_proyek,
                ['status' => $previousStatus],
                ['status' => $project->status],
            );
        });

        return to_route('admin.projects.index')->with('status', 'Status proyek berhasil diperbarui.');
    }

    public function recordPayment(
        Request $request,
        PaymentTerm $term,
        AdminAuditLogger $auditLogger,
    ): RedirectResponse {
        $validated = $request->validate([
            'tgl_bayar' => ['required', 'date'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'tgl_bayar.required' => 'Tanggal pembayaran wajib diisi.',
            'bukti.mimes' => 'Bukti harus berupa JPG, PNG, atau PDF.',
            'bukti.max' => 'Ukuran bukti maksimal 2 MB.',
        ]);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);

        $path = null;
        try {
            DB::transaction(function () use ($request, $term, $validated, $admin, $auditLogger, &$path): void {
                $lockedTerm = PaymentTerm::query()->lockForUpdate()->findOrFail($term->id_termin);
                abort_if($lockedTerm->status === 'lunas', 409, 'Termin ini sudah lunas.');
                $uploadedFile = $request->file('bukti');
                if ($uploadedFile !== null) {
                    $storedPath = $uploadedFile->store('bukti-pembayaran');
                    if (! is_string($storedPath)) {
                        throw new RuntimeException('Bukti pembayaran gagal disimpan.');
                    }
                    $path = $storedPath;
                }

                $before = [
                    'status' => $lockedTerm->status,
                    'jumlah_dibayar' => $lockedTerm->jumlah_dibayar,
                ];
                $lockedTerm->update([
                    'status' => 'lunas',
                    'jumlah_dibayar' => $lockedTerm->nominal,
                    'tgl_bayar' => $validated['tgl_bayar'],
                    'bukti_path' => $path,
                ]);
                $auditLogger->record(
                    $admin,
                    'pembayaran_dicatat',
                    'termin_pembayaran',
                    (int) $lockedTerm->id_termin,
                    $before,
                    ['status' => 'lunas', 'jumlah_dibayar' => $lockedTerm->nominal],
                );
            });
        } catch (Throwable $exception) {
            if ($path !== null && ! Storage::delete($path)) {
                report(new RuntimeException('Bukti pembayaran yatim gagal dihapus setelah transaksi gagal.'));
            }

            throw $exception;
        }

        return to_route('admin.projects.index')->with('status', 'Pembayaran termin berhasil dicatat.');
    }
}
