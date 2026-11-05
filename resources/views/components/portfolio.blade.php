@props(['portfolios' => []])

<section id="portofolio" class="py-20 lg:py-28 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-12 lg:mb-16">
            <span class="text-[#D32F2F] font-bold text-xs sm:text-sm tracking-widest uppercase block mb-3">
                PORTOFOLIO PILIHAN
            </span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight mb-3">
                Kegiatan yang telah kami wujudkan
            </h2>
            <p class="text-gray-500 text-sm sm:text-base">
                Klik kegiatan untuk melihat dokumentasi lengkapnya.
            </p>
        </div>

        <!-- Portfolio Carousel Track & Controls -->
        <div class="relative max-w-6xl mx-auto">
            
            <!-- Floating Navigation Buttons -->
            <button id="portfolio-prev" type="button" class="absolute -left-2 sm:-left-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-white text-gray-800 shadow-xl border border-gray-100 flex items-center justify-center hover:bg-gray-50 hover:scale-105 transition-all" aria-label="Portofolio sebelumnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <button id="portfolio-next" type="button" class="absolute -right-2 sm:-right-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-white text-gray-800 shadow-xl border border-gray-100 flex items-center justify-center hover:bg-gray-50 hover:scale-105 transition-all" aria-label="Portofolio berikutnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Cards Container -->
            <div id="portfolio-container" class="flex items-center gap-4 sm:gap-6 overflow-x-auto scrollbar-none py-8 px-4 scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach ($portfolios as $idx => $p)
                    <div class="portfolio-item flex-shrink-0 cursor-pointer transition-all duration-300 group"
                         data-index="{{ $idx }}"
                         data-active="{{ $p['is_active'] ? 'true' : 'false' }}">
                        
                        <div class="portfolio-card relative w-60 sm:w-68 md:w-72 h-88 sm:h-96 rounded-2xl overflow-hidden transition-all duration-300 shadow-md {{ $p['is_active'] ? 'ring-4 ring-[#D32F2F]/20 scale-105 shadow-2xl' : 'opacity-80 hover:opacity-100' }}">
                            <!-- Image / Visual Background -->
                            <img src="{{ $p['image'] }}" alt="{{ $p['title'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            
                            <!-- Dark Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-transparent"></div>

                            <!-- Top Badges -->
                            <div class="absolute top-4 left-4 right-4 flex items-center justify-between">
                                <span class="bg-black/40 backdrop-blur-md text-white/90 text-xs px-2.5 py-1 rounded-full border border-white/20 font-medium">
                                    {{ $p['category'] }} / {{ $p['year'] }}
                                </span>
                            </div>

                            <!-- Bottom Content -->
                            <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                                <div class="flex items-center gap-1.5 text-xs text-gray-300 mb-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#D32F2F]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $p['location'] }}</span>
                                </div>

                                <h3 class="text-base sm:text-lg font-bold tracking-tight text-white mb-3 leading-snug">
                                    {{ $p['title'] }}
                                </h3>

                                <!-- Active Button (Shown on active card) -->
                                <div class="active-cta {{ $p['is_active'] ? 'block' : 'hidden' }}">
                                    <a href="#kontak" class="inline-flex items-center gap-1.5 bg-[#D32F2F] hover:bg-[#B71C1C] text-white text-xs font-semibold px-4 py-2 rounded-full transition-colors">
                                        <span>Lihat dokumentasi</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Dots Indicator -->
            <div id="portfolio-dots" class="flex items-center justify-center gap-2 mt-4">
                @foreach ($portfolios as $idx => $p)
                    <button type="button" class="portfolio-dot w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $p['is_active'] ? 'bg-[#D32F2F] w-7' : 'bg-gray-300 hover:bg-gray-400' }}" data-index="{{ $idx }}" aria-label="Slide {{ $idx + 1 }}"></button>
                @endforeach
            </div>

            <!-- Bottom Button -->
            <div class="text-center mt-10">
                <a href="#portofolio" class="inline-flex items-center gap-2 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-800 text-sm font-semibold px-6 py-2.5 rounded-full shadow-sm transition-all duration-200">
                    <span>Semua portofolio</span>
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

        </div>

    </div>
</section>
