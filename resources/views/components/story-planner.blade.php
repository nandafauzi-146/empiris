@props(['whatsappNumber' => config('empirise.whatsapp_number', '6281234567890')])

<section class="story-planner-section py-12 bg-gradient-to-b from-gray-50 to-white border-y border-gray-100" id="rencana-ai" x-data="{
    story: '',
    tipeKlien: '',
    durasiHari: 2,
    loading: false,
    errorMessage: '',
    result: null,
    activeDay: 1,
    samples: [
        {
            label: 'Gathering Kantor di Yogya (3 Hari)',
            tipe: 'korporat',
            durasi: 3,
            text: 'Gathering 40 orang karyawan di Yogyakarta untuk bonding, suasana santai, dan wisata kuliner khas selama 3 hari 2 malam.'
        },
        {
            label: 'Studi Lapangan Kampus ke Bali (4 Hari)',
            tipe: 'kampus',
            durasi: 4,
            text: 'Studi lapangan 60 mahasiswa arsitektur ke Bali mengamati tata ruang tradisional, cagar budaya, dan lanskap alam selama 4 hari.'
        },
        {
            label: 'Outbound Instansi di Dieng (2 Hari)',
            tipe: 'instansi',
            durasi: 2,
            text: 'Outbound fun games dan penyegaran untuk 30 staf instansi di kawasan pegunungan sejuk Dieng selama 2 hari 1 malam.'
        }
    ],
    applySample(sample) {
        this.story = sample.text;
        this.tipeKlien = sample.tipe;
        this.durasiHari = sample.durasi;
        this.errorMessage = '';
    },
    async submitStory() {
        if (!this.story || this.story.trim().length < 10) {
            this.errorMessage = 'Ceritakan rencana Anda minimal 10 karakter.';
            return;
        }
        this.loading = true;
        this.errorMessage = '';
        this.result = null;

        try {
            const res = await fetch('{{ route('recommendation.story') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    cerita: this.story,
                    tipe_klien: this.tipeKlien || null,
                    durasi_hari: this.durasiHari ? parseInt(this.durasiHari) : null
                })
            });

            const data = await res.json();
            if (!res.ok) {
                this.errorMessage = data.message || 'Terjadi kendala saat memproses cerita. Silakan coba sesaat lagi.';
            } else {
                this.result = data.data;
                this.activeDay = 1;
                this.$nextTick(() => {
                    document.getElementById('hasil-rekomendasi-ai')?.scrollIntoView({ behavior: 'smooth' });
                });
            }
        } catch (err) {
            this.errorMessage = 'Koneksi terganggu. Silakan periksa jaringan Anda atau hubungi kami langsung.';
        } finally {
            this.loading = false;
        }
    }
}">
    <div class="page-shell max-w-5xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-8">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 uppercase tracking-wider mb-2">
                Eksplorasi Ide & Rencana Kegiatan
            </span>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                Ceritakan Kebutuhan Anda, AI Kami Memahami Terlebih Dahulu
            </h2>
            <p class="mt-2 text-sm sm:text-base text-gray-600 max-w-2xl mx-auto">
                Tulis rencana perjalanan atau kegiatan rombongan Anda. Sistem kami akan menangkap inti kebutuhan, menyarankan destinasi terbaik, dan menyusun draf gambaran awal secara instan tanpa komitmen biaya.
            </p>
        </div>

        <!-- Card Input Cerita -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <!-- Pilihan Segmen & Durasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                        Segmen / Kategori Rombongan
                    </label>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" @click="tipeKlien = 'korporat'" :class="tipeKlien === 'korporat' ? 'bg-red-600 text-white font-medium border-red-600' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border-gray-200'" class="py-2 text-xs rounded-xl border text-center transition">
                            Korporat
                        </button>
                        <button type="button" @click="tipeKlien = 'kampus'" :class="tipeKlien === 'kampus' ? 'bg-red-600 text-white font-medium border-red-600' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border-gray-200'" class="py-2 text-xs rounded-xl border text-center transition">
                            Kampus
                        </button>
                        <button type="button" @click="tipeKlien = 'instansi'" :class="tipeKlien === 'instansi' ? 'bg-red-600 text-white font-medium border-red-600' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border-gray-200'" class="py-2 text-xs rounded-xl border text-center transition">
                            Instansi
                        </button>
                        <button type="button" @click="tipeKlien = 'umum'" :class="tipeKlien === 'umum' ? 'bg-red-600 text-white font-medium border-red-600' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border-gray-200'" class="py-2 text-xs rounded-xl border text-center transition">
                            Umum
                        </button>
                    </div>
                </div>

                <div>
                    <label for="input-durasi-hari" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                        Perkiraan Durasi (Hari)
                    </label>
                    <div class="flex items-center space-x-2">
                        <input id="input-durasi-hari" type="number" min="1" max="14" x-model="durasiHari" class="w-24 px-3 py-2 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <span class="text-xs text-gray-500">Hari kegiatan (fleksibel dapat disesuaikan)</span>
                    </div>
                </div>
            </div>

            <!-- Contoh Cerita Cepat -->
            <div class="mb-4">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1.5">
                    Contoh Cepat (Klik untuk mencoba):
                </span>
                <div class="flex flex-wrap gap-2">
                    <template x-for="(sample, idx) in samples" :key="idx">
                        <button type="button" @click="applySample(sample)" class="text-xs bg-gray-100 hover:bg-red-50 hover:text-red-700 hover:border-red-200 border border-transparent text-gray-700 px-3 py-1.5 rounded-lg transition inline-flex items-center space-x-1">
                            <span class="font-medium" x-text="sample.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Textarea Cerita -->
            <div class="mb-4">
                <label for="textarea-cerita" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Ceritakan Kebutuhan, Anggota Rombongan, atau Ekspektasi Anda
                </label>
                <textarea id="textarea-cerita" x-model="story" rows="4" maxlength="1500" placeholder="Contoh: Kami dari divisi marketing ingin mengadakan outing 30 orang ke daerah sejuk selama 3 hari. Kami butuh aktivitas team building yang tidak terlalu melelahkan fisik, penginapan yang nyaman, serta makan malam kebersamaan..." class="w-full px-4 py-3 text-sm text-gray-900 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-y"></textarea>
                <div class="flex justify-between items-center mt-1 text-xs text-gray-500">
                    <span>Privasi terjaga: Kontak personal disamarkan otomatis sebelum diproses.</span>
                    <span x-text="(story ? story.length : 0) + ' / 1500 karakter'"></span>
                </div>
            </div>

            <!-- Pesan Error -->
            <div x-show="errorMessage" x-cloak class="p-3 mb-4 text-xs text-red-700 bg-red-50 rounded-xl border border-red-200">
                <span x-text="errorMessage"></span>
            </div>

            <!-- Tombol Submit -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                <div class="text-xs text-gray-500 flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Tanpa kewajiban harga &bull; Dapat dikonsultasikan lebih lanjut</span>
                </div>

                <button type="button" @click="submitStory()" :disabled="loading" class="w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium text-sm rounded-xl shadow-sm transition inline-flex items-center justify-center space-x-2 disabled:opacity-50">
                    <template x-if="!loading">
                        <span class="inline-flex items-center space-x-2">
                            <span>Analisis Cerita & Buat Rekomendasi</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="inline-flex items-center space-x-2">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>AI Sedang Memahami Cerita Anda...</span>
                        </span>
                    </template>
                </button>
            </div>
        </div>

        <!-- Hasil Rekomendasi AI -->
        <div id="hasil-rekomendasi-ai" x-show="result" x-cloak class="mt-8 bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
            <!-- Header Hasil dengan Badge Gambaran Awal -->
            <div class="bg-gradient-to-r from-red-600 to-rose-700 p-6 text-white">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white tracking-wide uppercase">
                        Gambaran Awal &bull; AI Rekomendasi
                    </span>
                    <span class="text-xs text-white/80">
                        Tanpa Komitmen Biaya
                    </span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold">
                    Konsep Rencana Perjalanan Anda
                </h3>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <!-- 1. Yang Kami Pahami dari Cerita Anda (CHG-01 Wajib) -->
                <div class="bg-rose-50/70 border border-rose-100 rounded-xl p-5">
                    <div class="flex items-center space-x-2 text-red-700 font-semibold text-sm mb-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Yang Kami Pahami dari Cerita Anda</span>
                    </div>
                    <p class="text-sm sm:text-base text-gray-800 leading-relaxed italic" x-text="result?.pemahaman_ringkas"></p>
                    
                    <div class="mt-3 flex flex-wrap gap-2 items-center text-xs text-gray-600">
                        <span class="font-medium text-gray-700">Layanan Direkomendasikan:</span>
                        <template x-for="(layanan, idx) in result?.layanan_disarankan || []" :key="idx">
                            <span class="bg-white px-2.5 py-1 rounded-md border border-rose-200 text-rose-800 font-medium" x-text="layanan"></span>
                        </template>
                    </div>
                </div>

                <!-- 2. Tiga Destinasi Pilihan (CHG-07 Destinasi Bebas / Database) -->
                <div>
                    <h4 class="text-base font-bold text-gray-900 mb-3">
                        Tiga Destinasi yang Paling Sesuai
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <template x-for="(dest, idx) in result?.destinasi || []" :key="idx">
                            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/50 hover:bg-white hover:shadow-sm transition flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-1 mb-2">
                                        <h5 class="font-bold text-gray-900 text-base" x-text="dest.nama"></h5>
                                        <span :class="dest.sumber === 'bebas' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200'" class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full border">
                                            <span x-text="dest.sumber === 'bebas' ? 'Tujuan Bebas' : 'Database'"></span>
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed mb-3" x-text="dest.alasan"></p>
                                </div>
                                <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between text-xs text-gray-500">
                                    <span>Skor Kesesuaian</span>
                                    <span class="font-bold text-red-600" x-text="dest.kecocokan + '%'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 3. Susunan Hari Kegiatan -->
                <div x-show="result?.hari && result.hari.length > 0">
                    <h4 class="text-base font-bold text-gray-900 mb-3">
                        Gambaran Rencana Hari Kegiatan
                    </h4>
                    <!-- Tab Hari -->
                    <div class="flex border-b border-gray-200 space-x-2 mb-4 overflow-x-auto pb-1">
                        <template x-for="day in result?.hari || []" :key="day.hari">
                            <button type="button" @click="activeDay = day.hari" :class="activeDay === day.hari ? 'border-red-600 text-red-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-2 px-4 border-b-2 text-sm whitespace-nowrap transition">
                                <span x-text="'Hari ' + day.hari"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Isi Hari Terpilih -->
                    <template x-for="day in result?.hari || []" :key="day.hari">
                        <div x-show="activeDay === day.hari" class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <h5 class="font-bold text-gray-900 text-sm mb-2" x-text="day.judul"></h5>
                            <ul class="space-y-1.5 text-xs sm:text-sm text-gray-700 list-disc list-inside">
                                <template x-for="(keg, kidx) in day.kegiatan" :key="kidx">
                                    <li x-text="keg"></li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>

                <!-- 4. Pertanyaan Lanjutan -->
                <div x-show="result?.pertanyaan_lanjutan && result.pertanyaan_lanjutan.length > 0" class="text-xs text-gray-600 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <span class="font-semibold text-gray-800 block mb-1">Pertanyaan Pendukung untuk Tim Konsultan Kami:</span>
                    <ul class="list-disc list-inside space-y-1">
                        <template x-for="(tanya, tidx) in result.pertanyaan_lanjutan" :key="tidx">
                            <li x-text="tanya"></li>
                        </template>
                    </ul>
                </div>

                <!-- 5. Disclaimer Wajib (REQ-ITN-09) -->
                <div class="text-[11px] text-gray-500 bg-amber-50/60 border border-amber-200/60 p-3 rounded-lg leading-relaxed">
                    <strong>Pemberitahuan:</strong> <span x-text="result?.disclaimer"></span>
                </div>

                <!-- 6. Tombol Aksi Lanjutan (Pesan & Konsultasi Resmi) -->
                <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a :href="'{{ route('consultation.create') }}?cerita=' + encodeURIComponent(story) + '&tipe=' + encodeURIComponent(tipeKlien || '') + '&durasi=' + encodeURIComponent(durasiHari || '')" class="w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium text-sm rounded-xl shadow-sm text-center transition">
                        Lanjutkan ke Formulir Pemesanan & Konsultasi
                    </a>

                    <a :href="'https://wa.me/{{ $whatsappNumber }}?text=' + encodeURIComponent('Halo Tim Empirise, saya ingin berdiskusi mengenai rencana kegiatan: ' + story.substring(0, 200))" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-xl shadow-sm text-center transition inline-flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Diskusi Cepat via WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
