<?php

namespace App\Models;

use App\Enums\KonsultasiStatus;
use App\Enums\UserRole;
use Database\Factories\ConsultationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'id_klien',
    'nomor_konsultasi',
    'nama',
    'email',
    'telepon',
    'instansi',
    'layanan',
    'tujuan',
    'jumlah_peserta',
    'durasi_hari',
    'rentang_budget',
    'tanggal_rencana',
    'minat',
    'cerita_kebutuhan',
    'jenis_kendaraan',
    'lokasi_jemput',
    'jam_jemput',
    'tanggal_kembali',
    'status',
])]
class Consultation extends Model
{
    /** @use HasFactory<ConsultationFactory> */
    use HasFactory;

    protected $table = 'konsultasi';

    protected $primaryKey = 'id_konsultasi';

    protected function casts(): array
    {
        return [
            'tanggal_rencana' => 'date',
            'tanggal_kembali' => 'date',
            'status' => KonsultasiStatus::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_klien');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'id_konsultasi', 'id_konsultasi');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ConsultationReply::class, 'id_konsultasi', 'id_konsultasi')
            ->oldest('created_at')
            ->oldest('id_balasan');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'id_konsultasi', 'id_konsultasi');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Penawaran::class, 'id_konsultasi', 'id_konsultasi')
            ->latest('versi');
    }

    public function scopeNeedsAdminResponse(Builder $query): Builder
    {
        return $query
            ->whereIn('status', [KonsultasiStatus::Baru->value, KonsultasiStatus::Dibalas->value])
            ->whereRaw(
                'NOT EXISTS (
                    SELECT 1
                    FROM balasan_konsultasi AS latest_reply
                    INNER JOIN users AS latest_sender ON latest_sender.id = latest_reply.id_pengirim
                    WHERE latest_reply.id_konsultasi = konsultasi.id_konsultasi
                      AND latest_reply.id_balasan = (
                          SELECT MAX(newer_reply.id_balasan)
                          FROM balasan_konsultasi AS newer_reply
                          WHERE newer_reply.id_konsultasi = konsultasi.id_konsultasi
                      )
                      AND latest_sender.role = ?
                )',
                [UserRole::Admin->value],
            );
    }
}
