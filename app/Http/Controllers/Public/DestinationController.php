<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdminContent;
use Illuminate\Contracts\View\View;

class DestinationController extends Controller
{
    private const DEFAULT_DESTINATIONS = [
        'yogyakarta' => ['Yogyakarta', 'Jawa Tengah & DIY', 'Budaya', 'Kota budaya dengan candi, keraton, kuliner, dan pusat kerajinan.'],
        'solo' => ['Solo', 'Jawa Tengah & DIY', 'Budaya', 'Kota batik dan keraton dengan kuliner khas dan suasana yang tenang.'],
        'semarang' => ['Semarang', 'Jawa Tengah & DIY', 'Budaya', 'Jelajahi kota tua, kekayaan kuliner, dan bangunan bersejarah.'],
        'dieng' => ['Dieng', 'Jawa Tengah & DIY', 'Alam', 'Nikmati udara pegunungan, telaga, dan lanskap alam dataran tinggi.'],
        'malang' => ['Malang', 'Jawa Timur', 'Alam', 'Temukan suasana sejuk, wisata alam, dan ragam kuliner khas.'],
        'bali' => ['Bali', 'Bali', 'Alam', 'Rancang perjalanan untuk menikmati alam, budaya, dan pesisir Bali.'],
        'lombok' => ['Lombok', 'Nusa Tenggara', 'Alam', 'Jelajahi pantai, pulau-pulau kecil, dan pemandangan Lombok.'],
        'labuan-bajo' => ['Labuan Bajo', 'Nusa Tenggara', 'Alam', 'Mulai perjalanan laut dan jelajahi pesona kepulauan.'],
        'bandung' => ['Bandung', 'Jawa Barat', 'Alam', 'Nikmati udara sejuk, suasana kota, dan destinasi sekitar Bandung.'],
        'jakarta' => ['Jakarta', 'DKI Jakarta', 'Kota', 'Jelajahi ragam pengalaman kota dan destinasi bersejarah.'],
        'banyuwangi' => ['Banyuwangi', 'Jawa Timur', 'Alam', 'Temukan wisata alam dan budaya di ujung timur Pulau Jawa.'],
        'karimunjawa' => ['Karimunjawa', 'Jawa Tengah & DIY', 'Alam', 'Rencanakan perjalanan ke gugusan pulau dan perairan Karimunjawa.'],
    ];

    public function index(): View
    {
        $managed = AdminContent::query()
            ->where('jenis', 'destinasi')
            ->orderBy('urutan')
            ->orderBy('judul')
            ->get();

        $destinations = $managed->isEmpty()
            ? collect(self::DEFAULT_DESTINATIONS)
                ->map(fn (array $destination, string $slug): array => [
                    'name' => $destination[0],
                    'region' => $destination[1],
                    'category' => $destination[2],
                    'slug' => $slug,
                    'description' => $destination[3],
                    'image' => 'images/figma/'.$slug.'.png',
                ])
            : $managed
                ->where('aktif', true)
                ->where('status', 'tampil')
                ->sortBy(['urutan', 'judul'])
                ->map(function (AdminContent $content): array {
                    $slug = $content->slug ?: str($content->judul)->slug()->toString();
                    $metadata = $content->metadata ?? [];

                    return [
                        'name' => $content->judul,
                        'region' => $this->metadataValue($metadata, ['region', 'wilayah'], 'Indonesia'),
                        'category' => $this->metadataValue($metadata, ['category', 'kategori'], 'Wisata'),
                        'slug' => $slug,
                        'description' => $content->deskripsi ?? '',
                        'image' => $this->metadataValue($metadata, ['image', 'gambar'], 'images/figma/solo.png'),
                    ];
                });

        return view('pages.destinations.index', [
            'destinations' => $destinations->values(),
        ]);
    }

    public function show(string $slug): View
    {
        $managedContents = AdminContent::query()
            ->where('jenis', 'destinasi')
            ->get();
        $managed = $managedContents->first(fn (AdminContent $content): bool => (
            ($content->slug ?: str($content->judul)->slug()->toString()) === $slug
        ));

        if ($managed !== null) {
            abort_unless($managed->aktif && $managed->status === 'tampil', 404);
            $metadata = $managed->metadata ?? [];
            $destination = [
                'name' => $managed->judul,
                'region' => $this->metadataValue($metadata, ['region', 'wilayah'], 'Indonesia'),
                'category' => $this->metadataValue($metadata, ['category', 'kategori'], 'Wisata'),
                'slug' => $slug,
                'description' => $managed->deskripsi ?? '',
                'image' => $this->metadataValue($metadata, ['image', 'gambar'], 'images/figma/solo.png'),
            ];
        } elseif ($managedContents->isNotEmpty()) {
            abort(404);
        } elseif (isset(self::DEFAULT_DESTINATIONS[$slug])) {
            [$name, $region, $category, $description] = self::DEFAULT_DESTINATIONS[$slug];
            $destination = [
                'name' => $name,
                'region' => $region,
                'category' => $category,
                'slug' => $slug,
                'description' => $description,
                'image' => 'images/figma/'.$slug.'.png',
            ];
        } else {
            abort(404);
        }

        return view('pages.destinations.show', ['destination' => $destination]);
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @param  list<string>  $keys
     */
    private function metadataValue(array $metadata, array $keys, string $fallback): string
    {
        foreach ($keys as $key) {
            if (isset($metadata[$key]) && is_scalar($metadata[$key])) {
                return (string) $metadata[$key];
            }
        }

        return $fallback;
    }
}
