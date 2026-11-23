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
    public function buat(Consultation $consultation, array $details, ?string $catatanAdmin = null): Penawaran
    {
        return DB::transaction(function () use ($consultation, $details, $catatanAdmin): Penawaran {
            $latestVersion = (int) Penawaran::query()
                ->where('id_konsultasi', $consultation->getKey())
                ->lockForUpdate()
                ->max('versi');

            $offer = Penawaran::query()->create([
                'id_konsultasi' => $consultation->getKey(),
                'versi' => $latestVersion + 1,
                'status' => PenawaranStatus::Draft,
                'judul' => 'Penawaran '.$consultation->layanan,
                'catatan_admin' => $catatanAdmin,
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

            $this->hitungTotal($offer);

            return $offer->fresh(['details']);
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
                'catatan_admin' => $lama->catatan_admin,
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

            $this->hitungTotal($baru);

            return $baru->fresh(['details']);
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
