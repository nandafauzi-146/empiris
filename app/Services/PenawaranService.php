<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\KonsultasiStatus;
use App\Enums\PenawaranStatus;
use App\Models\Consultation;
use App\Models\DetailPenawaran;
use App\Models\Penawaran;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PenawaranService
{
    /**
     * Buat penawaran / proposal baru untuk konsultasi.
     *
     * @param  array<int, array{nama_item: string, deskripsi?: ?string, jumlah: numeric, satuan: string, harga_satuan?: ?int, asal?: string}>  $details
     * @param  array<string, mixed>  $proposalData
     */
    public function buat(
        Consultation $consultation,
        array $details,
        ?string $catatanAdmin = null,
        array $proposalData = []
    ): Penawaran {
        return DB::transaction(function () use ($consultation, $details, $catatanAdmin, $proposalData): Penawaran {
            $latestVersion = (int) Penawaran::query()
                ->where('id_konsultasi', $consultation->getKey())
                ->lockForUpdate()
                ->max('versi');

            $configProposal = config('empirise.proposal', []);

            $offer = Penawaran::query()->create([
                'id_konsultasi' => $consultation->getKey(),
                'versi' => $latestVersion + 1,
                'status' => PenawaranStatus::Draft,
                'judul' => $proposalData['judul'] ?? ('Penawaran '.$consultation->layanan),
                'pemahaman' => $proposalData['pemahaman'] ?? null,
                'catatan_admin' => $catatanAdmin,
                'cakupan_termasuk' => $proposalData['cakupan_termasuk'] ?? ($configProposal['cakupan_termasuk'] ?? null),
                'cakupan_tidak' => $proposalData['cakupan_tidak'] ?? ($configProposal['cakupan_tidak'] ?? null),
                'keselamatan' => $proposalData['keselamatan'] ?? ($configProposal['keselamatan'] ?? null),
                'syarat' => $proposalData['syarat'] ?? ($configProposal['syarat'] ?? null),
                'berlaku_hari' => $proposalData['berlaku_hari'] ?? ($configProposal['berlaku_hari'] ?? 14),
                'logo_path' => $proposalData['logo_path'] ?? null,
                'total' => 0,
            ]);

            foreach (array_values($details) as $index => $detail) {
                $offer->details()->create([
                    'urutan' => $index + 1,
                    'nama_item' => $detail['nama_item'],
                    'deskripsi' => $detail['deskripsi'] ?? null,
                    'jumlah' => $detail['jumlah'],
                    'satuan' => $detail['satuan'],
                    'harga_satuan' => $detail['harga_satuan'] ?? null,
                    'asal' => $detail['asal'] ?? 'manual',
                ]);
            }

            if (! empty($proposalData['days']) && is_array($proposalData['days'])) {
                foreach (array_values($proposalData['days']) as $dIndex => $day) {
                    if (! empty($day['judul']) || ! empty($day['isi'])) {
                        $offer->days()->create([
                            'urutan' => $dIndex + 1,
                            'judul' => mb_substr((string) ($day['judul'] ?? 'Hari '.($dIndex + 1)), 0, 60),
                            'isi' => (string) ($day['isi'] ?? ''),
                        ]);
                    }
                }
            }

            $this->hitungTotal($offer);

            return $offer->fresh(['details', 'days', 'photos']);
        });
    }

    public function kirim(Penawaran $offer): void
    {
        if ($offer->hasEmptyPrice()) {
            abort(422, 'Masih ada komponen penawaran yang belum memiliki harga.');
        }

        DB::transaction(function () use ($offer): void {
            $lockedOffer = Penawaran::query()->lockForUpdate()->findOrFail($offer->getKey());
            abort_unless($lockedOffer->status === PenawaranStatus::Draft, 409);
            $this->hitungTotal($lockedOffer);
            $lockedOffer->update([
                'status' => PenawaranStatus::Dikirim,
                'dikirim_at' => now(),
            ]);
            $lockedOffer->consultation()->update(['status' => KonsultasiStatus::Penawaran]);
        });
    }

    public function setujui(Penawaran $offer, User $client): Project
    {
        return DB::transaction(function () use ($offer, $client): Project {
            $lockedOffer = Penawaran::query()
                ->with('consultation')
                ->lockForUpdate()
                ->findOrFail($offer->getKey());

            abort_unless(
                $lockedOffer->status === PenawaranStatus::Dikirim
                && $lockedOffer->consultation->id_klien === $client->getKey(),
                409,
            );

            $lockedOffer->update([
                'status' => PenawaranStatus::Disetujui,
                'ditanggapi_at' => now(),
                'disetujui_at' => now(),
            ]);
            $lockedOffer->consultation()->update(['status' => KonsultasiStatus::Deal]);

            $consultation = $lockedOffer->consultation;
            $start = $consultation->tanggal_rencana ?? now()->toDateString();
            $duration = max(1, (int) ($consultation->durasi_hari ?? 1));

            $project = Project::query()->create([
                'id_penawaran' => $lockedOffer->getKey(),
                'id_konsultasi' => $consultation->getKey(),
                'id_klien' => $client->getKey(),
                'nama' => $lockedOffer->judul,
                'tgl_mulai' => $start,
                'tgl_selesai' => date('Y-m-d', strtotime($start.' +'.($duration - 1).' days')),
                'status' => 'terjadwal',
                'total_nilai' => $lockedOffer->total,
            ]);
            $project->update(['nomor_proyek' => 'PR-'.str_pad((string) $project->id_proyek, 4, '0', STR_PAD_LEFT)]);

            return $project;
        });
    }

    public function mintaRevisi(Penawaran $offer, User $client, string $catatan): void
    {
        DB::transaction(function () use ($offer, $client, $catatan): void {
            $lockedOffer = Penawaran::query()->with('consultation')->lockForUpdate()->findOrFail($offer->getKey());
            abort_unless(
                $lockedOffer->status === PenawaranStatus::Dikirim
                && $lockedOffer->consultation->id_klien === $client->getKey(),
                409,
            );
            $lockedOffer->update([
                'status' => PenawaranStatus::Revisi,
                'catatan_klien' => $catatan,
                'ditanggapi_at' => now(),
            ]);
            $lockedOffer->consultation()->update(['status' => KonsultasiStatus::Dibalas]);
        });
    }

    public function tolak(Penawaran $offer, User $client, ?string $catatan = null): void
    {
        DB::transaction(function () use ($offer, $client, $catatan): void {
            $lockedOffer = Penawaran::query()->with('consultation')->lockForUpdate()->findOrFail($offer->getKey());
            abort_unless(
                $lockedOffer->status === PenawaranStatus::Dikirim
                && $lockedOffer->consultation->id_klien === $client->getKey(),
                409,
            );
            $lockedOffer->update([
                'status' => PenawaranStatus::Ditolak,
                'catatan_klien' => $catatan,
                'ditanggapi_at' => now(),
            ]);
        });
    }

    /**
     * Buat versi baru proposal dari proposal sebelumnya yang direvisi/dikirim.
     * Menyalin seluruh isi proposal, rincian biaya, hari kegiatan, dan foto (R12, CHG-06).
     */
    public function buatVersiBaru(Penawaran $lama): Penawaran
    {
        return DB::transaction(function () use ($lama): Penawaran {
            abort_unless(
                in_array($lama->status, [PenawaranStatus::Revisi, PenawaranStatus::Dikirim], true),
                409,
            );

            $latestVersion = (int) Penawaran::query()
                ->where('id_konsultasi', $lama->id_konsultasi)
                ->lockForUpdate()
                ->max('versi');

            $baru = Penawaran::query()->create([
                'id_konsultasi' => $lama->id_konsultasi,
                'versi' => $latestVersion + 1,
                'status' => PenawaranStatus::Draft,
                'judul' => $lama->judul,
                'pemahaman' => $lama->pemahaman,
                'catatan_admin' => $lama->catatan_admin,
                'cakupan_termasuk' => $lama->cakupan_termasuk,
                'cakupan_tidak' => $lama->cakupan_tidak,
                'keselamatan' => $lama->keselamatan,
                'syarat' => $lama->syarat,
                'berlaku_hari' => $lama->berlaku_hari,
                'logo_path' => $lama->logo_path,
                'total' => 0,
            ]);

            foreach ($lama->details as $detail) {
                $baru->details()->create([
                    'urutan' => $detail->urutan,
                    'nama_item' => $detail->nama_item,
                    'deskripsi' => $detail->deskripsi,
                    'jumlah' => $detail->jumlah,
                    'satuan' => $detail->satuan,
                    'harga_satuan' => $detail->harga_satuan,
                    'asal' => $detail->asal,
                ]);
            }

            foreach ($lama->days as $day) {
                $baru->days()->create([
                    'urutan' => $day->urutan,
                    'judul' => $day->judul,
                    'isi' => $day->isi,
                ]);
            }

            foreach ($lama->photos as $photo) {
                $baru->photos()->create([
                    'jenis' => $photo->jenis,
                    'nama_destinasi' => $photo->nama_destinasi,
                    'path' => $photo->path,
                    'keterangan' => $photo->keterangan,
                    'urutan' => $photo->urutan,
                ]);
            }

            $this->hitungTotal($baru);

            return $baru->fresh(['details', 'days', 'photos']);
        });
    }

    private function hitungTotal(Penawaran $offer): void
    {
        $total = $offer->details()
            ->get(['jumlah', 'harga_satuan'])
            ->sum(fn (DetailPenawaran $detail): int => (int) round((float) $detail->jumlah * (int) $detail->harga_satuan));

        $offer->update(['total' => $total]);
    }
}
