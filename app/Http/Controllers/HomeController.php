<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page with dummy data matching the Figma design.
     */
    public function index(): View
    {
        $destinations = [
            [
                'id' => 'solo',
                'name' => 'Solo',
                'province' => 'Jawa Tengah & DIY',
                'category' => 'Budaya',
                'tagline' => 'Jawa Tengah & DIY / Budaya',
                'description' => 'Kota batik dan keraton dengan kuliner khas, pasar tradisional, dan suasana yang tenang.',
                'image' => asset('assets/images/destinations/solo.svg'),
                'is_active' => true,
            ],
            [
                'id' => 'yogyakarta',
                'name' => 'Yogyakarta',
                'province' => 'Jawa Tengah & DIY',
                'category' => 'Budaya & Sejarah',
                'tagline' => 'Jawa Tengah & DIY / Budaya & Sejarah',
                'description' => 'Kota pelajar dan budaya dengan Malioboro, kemegahan keraton, dan candi-candi legendaris dunia.',
                'image' => asset('assets/images/destinations/yogyakarta.svg'),
                'is_active' => false,
            ],
            [
                'id' => 'semarang',
                'name' => 'Semarang',
                'province' => 'Jawa Tengah & DIY',
                'category' => 'Heritage & Maritim',
                'tagline' => 'Jawa Tengah & DIY / Heritage',
                'description' => 'Kota pelabuhan bersejarah dengan pesona Lawang Sewu, Kota Lama, dan ragam kuliner khas.',
                'image' => asset('assets/images/destinations/semarang.svg'),
                'is_active' => false,
            ],
            [
                'id' => 'dieng',
                'name' => 'Dieng',
                'province' => 'Jawa Tengah & DIY',
                'category' => 'Alam Pegunungan',
                'tagline' => 'Jawa Tengah & DIY / Alam',
                'description' => 'Negeri di atas awan bersuhu sejuk dengan kawah belerang, telaga warna, dan sunrise menakjubkan.',
                'image' => asset('assets/images/destinations/dieng.svg'),
                'is_active' => false,
            ],
            [
                'id' => 'malang',
                'name' => 'Malang',
                'province' => 'Jawa Timur',
                'category' => 'Pegunungan & Wisata',
                'tagline' => 'Jawa Timur / Pegunungan',
                'description' => 'Kota sejuk dikelilingi pegunungan dengan kebun apel, air terjun, dan pintu masuk menuju Bromo.',
                'image' => asset('assets/images/destinations/malang.svg'),
                'is_active' => false,
            ],
            [
                'id' => 'bali',
                'name' => 'Bali',
                'province' => 'Bali',
                'category' => 'Pantai & Budaya',
                'tagline' => 'Bali / Wisata Dunia',
                'description' => 'Pulau dewata dengan pantai eksotis, tradisi sakral mendalam, serta resor berkelas internasional.',
                'image' => asset('assets/images/destinations/bali.svg'),
                'is_active' => false,
            ],
            [
                'id' => 'lombok',
                'name' => 'Lombok',
                'province' => 'Nusa Tenggara',
                'category' => 'Bahari & Petualangan',
                'tagline' => 'Nusa Tenggara / Bahari',
                'description' => 'Gugusan gili memukau, pantai pasir putih Mandalika, dan kemegahan puncak Gunung Rinjani.',
                'image' => asset('assets/images/destinations/lombok.svg'),
                'is_active' => false,
            ],
            [
                'id' => 'labuan-bajo',
                'name' => 'Labuan Bajo',
                'province' => 'Nusa Tenggara',
                'category' => 'Petualangan Bahari',
                'tagline' => 'Nusa Tenggara / Eksotis',
                'description' => 'Gerbang utama menuju habitat asli Komodo dengan panorama Pulau Padar dan laut bergradasi.',
                'image' => asset('assets/images/destinations/labuan-bajo.svg'),
                'is_active' => false,
            ],
            [
                'id' => 'bandung',
                'name' => 'Bandung',
                'province' => 'Jawa Barat',
                'category' => 'Kota Kembang & Belanja',
                'tagline' => 'Jawa Barat / Lifestyle',
                'description' => 'Kota kreatif berhawa sejuk dengan arsitektur art deco, wisata alam kawah, dan surga belanja.',
                'image' => asset('assets/images/destinations/bandung.svg'),
                'is_active' => false,
            ],
            [
                'id' => 'jakarta',
                'name' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'category' => 'Metropolis & Heritage',
                'tagline' => 'DKI Jakarta / Metropolitan',
                'description' => 'Pusat dinamika urban Indonesia yang memadukan kawasan Kota Tua bersejarah dan gedung modern.',
                'image' => asset('assets/images/destinations/jakarta.svg'),
                'is_active' => false,
            ],
            [
                'id' => 'banyuwangi',
                'name' => 'Banyuwangi',
                'province' => 'Jawa Timur',
                'category' => 'Alam & Blue Fire',
                'tagline' => 'Jawa Timur / Petualangan',
                'description' => 'Keajaiban alam api biru di kawah Ijen serta savana Baluran di ujung timur pulau Jawa.',
                'image' => asset('assets/images/destinations/banyuwangi.svg'),
                'is_active' => false,
            ],
            [
                'id' => 'karimunjawa',
                'name' => 'Karimunjawa',
                'province' => 'Jawa Tengah & DIY',
                'category' => 'Bahari & Terumbu Karang',
                'tagline' => 'Jawa Tengah & DIY / Bahari',
                'description' => 'Kepulauan perawan dengan air laut sejernih kristal, terumbu karang hidup, dan ketenangan alami.',
                'image' => asset('assets/images/destinations/karimunjawa.svg'),
                'is_active' => false,
            ],
        ];

        $services = [
            [
                'title' => 'Tour & Wisata',
                'description' => 'Perjalanan wisata domestik dan internasional dengan itinerary yang pas dan nyaman untuk seluruh peserta.',
                'icon' => 'compass',
            ],
            [
                'title' => 'Event & Gathering',
                'description' => 'Perencanaan acara perusahaan, gathering, dan perayaan dari konsep hingga pelaksanaan di lapangan.',
                'icon' => 'sparkles',
            ],
            [
                'title' => 'Outbound',
                'description' => 'Program outbound dan fun games dengan fasilitator berpengalaman untuk meningkatkan kekompakan tim.',
                'icon' => 'mountain',
            ],
            [
                'title' => 'Rental Armada',
                'description' => 'Sewa bus, minibus, dan kendaraan pendukung lengkap dengan pengemudi profesional dan armada prima.',
                'icon' => 'bus',
            ],
            [
                'title' => 'Team Building',
                'description' => 'Modul pengembangan tim terukur dengan sesi refleksi mendalam dan instruktur bersertifikasi.',
                'icon' => 'users',
            ],
        ];

        $portfolios = [
            [
                'title' => 'Gathering Tahunan 400 Karyawan',
                'company' => 'Telkomsel',
                'category' => 'Korporat',
                'year' => '2024',
                'location' => 'Yogyakarta',
                'image' => asset('assets/images/portfolio/gathering-telkomsel.svg'),
                'is_active' => false,
            ],
            [
                'title' => 'Studi Lapangan Mahasiswa Arsitektur',
                'company' => 'Universitas',
                'category' => 'Kampus',
                'year' => '2024',
                'location' => 'Bali',
                'image' => asset('assets/images/portfolio/studi-lapangan-bali.svg'),
                'is_active' => false,
            ],
            [
                'title' => 'Outbound Pegawai Dinas',
                'company' => 'Dinas Pemerintahan',
                'category' => 'Instansi',
                'year' => '2023',
                'location' => 'Bogor',
                'image' => asset('assets/images/portfolio/outbound-bogor.svg'),
                'is_active' => true,
            ],
            [
                'title' => 'Team Building Startup Teknologi',
                'company' => 'Tech Enterprise',
                'category' => 'Korporat',
                'year' => '2025',
                'location' => 'Labuan Bajo',
                'image' => asset('assets/images/portfolio/team-building-labuan-bajo.svg'),
                'is_active' => false,
            ],
            [
                'title' => 'Wisata & Wisuda Angkatan',
                'company' => 'Fakultas Terpadu',
                'category' => 'Kampus',
                'year' => '2025',
                'location' => 'Malang',
                'image' => asset('assets/images/portfolio/wisata-malang.svg'),
                'is_active' => false,
            ],
            [
                'title' => 'Rapat Kerja Nasional Instansi',
                'company' => 'Kementerian',
                'category' => 'Instansi',
                'year' => '2025',
                'location' => 'Bandung',
                'image' => asset('assets/images/portfolio/rakernas-bandung.svg'),
                'is_active' => false,
            ],
        ];

        $clients = [
            ['name' => 'SMP Insan Rabbani', 'logo' => asset('assets/images/clients/smp-insan-rabbani.svg')],
            ['name' => 'Universitas Teknologi Yogyakarta', 'logo' => asset('assets/images/clients/uty.svg')],
            ['name' => 'Universitas Ahmad Dahlan', 'logo' => asset('assets/images/clients/uad.svg')],
            ['name' => 'Universitas Negeri Yogyakarta', 'logo' => asset('assets/images/clients/uny.svg')],
            ['name' => 'Komisi Pemilihan Umum', 'logo' => asset('assets/images/clients/kpu.svg')],
            ['name' => "Madrasah Mu'allimin Muhammadiyah", 'logo' => asset('assets/images/clients/muallimin.svg')],
            ['name' => 'BAZNAS', 'logo' => asset('assets/images/clients/baznas.svg')],
            ['name' => 'The Rain Official', 'logo' => asset('assets/images/clients/the-rain.svg')],
            ['name' => 'Pengadilan Negeri Wates', 'logo' => asset('assets/images/clients/pn-wates.svg')],
            ['name' => 'Ngatmombilung', 'logo' => asset('assets/images/clients/ngatmombilung.svg')],
            ['name' => 'Pendheza', 'logo' => asset('assets/images/clients/pendheza.svg')],
            ['name' => 'Kementerian RI', 'logo' => asset('assets/images/clients/kemenag.svg')],
            ['name' => 'Kementerian Pendidikan Dasar & Menengah', 'logo' => asset('assets/images/clients/kemendikdasmen.svg')],
            ['name' => 'Kereta Api Indonesia (KAI)', 'logo' => asset('assets/images/clients/kai.svg')],
            ['name' => 'Pertamina', 'logo' => asset('assets/images/clients/pertamina.svg')],
            ['name' => 'Adhi Karya (Persero)', 'logo' => asset('assets/images/clients/adhi-karya.svg')],
            ['name' => 'IALF Language Centre', 'logo' => asset('assets/images/clients/ialf.svg')],
            ['name' => 'Samsung Electronics', 'logo' => asset('assets/images/clients/samsung.svg')],
            ['name' => 'KONE Corporation', 'logo' => asset('assets/images/clients/kone.svg')],
            ['name' => 'Rocket Chicken Indonesia', 'logo' => asset('assets/images/clients/rocket-chicken.svg')],
            ['name' => 'Muhammadiyah Wirobrajan', 'logo' => asset('assets/images/clients/muhammadiyah.svg')],
            ['name' => 'Institut Pendidikan Yogyakarta', 'logo' => asset('assets/images/clients/ipy.svg')],
            ['name' => 'Politeknik YKPN', 'logo' => asset('assets/images/clients/poltek-ykpn.svg')],
        ];

        $testimonials = [
            [
                'quote' => 'Eksekusi rapi, tim sangat responsif sampai bus terakhir kembali dengan selamat.',
                'author' => 'HR Manager',
                'company' => 'perusahaan logistik',
                'rating' => 5,
            ],
            [
                'quote' => 'Itinerary edukatif dan sesuai anggaran kampus. Mahasiswa sangat terkesan dan antusias.',
                'author' => 'Dosen Pembimbing',
                'company' => 'Fakultas Teknik',
                'rating' => 5,
            ],
            [
                'quote' => 'Peserta antusias dan seluruh rangkaian kegiatan berjalan aman dan tertib dari awal hingga selesai.',
                'author' => 'Kepala Bagian Umum',
                'company' => 'Instansi Pemerintah',
                'rating' => 5,
            ],
        ];

        return view('home', compact(
            'destinations',
            'services',
            'portfolios',
            'clients',
            'testimonials'
        ));
    }
}
