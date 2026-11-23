<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['jenis', 'judul', 'slug', 'deskripsi', 'metadata', 'status', 'aktif', 'urutan'])]
class AdminContent extends Model
{
    public const TYPES = [
        'tim' => 'Tim',
        'layanan' => 'Layanan',
        'portofolio' => 'Portofolio',
        'destinasi' => 'Destinasi',
        'faq' => 'FAQ',
        'klien' => 'Klien',
        'testimoni' => 'Testimoni',
    ];

    protected $table = 'konten_admin';

    protected $primaryKey = 'id_konten';

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'aktif' => 'boolean',
            'urutan' => 'integer',
        ];
    }
}
