@props(['destinations' => []])

<section class="relative min-h-screen flex items-center overflow-hidden pt-24 pb-16 lg:pt-28 lg:pb-20 bg-[#0F172A]" id="hero-section">
    <!-- Hero Background Image & Gradient Overlays -->
    <div class="absolute inset-0 z-0">
        <img id="hero-bg-img" 
             src="{{ asset('assets/images/hero-bg.svg') }}" 
             alt="Empirise Jalan-Jalan Hero" 
             class="w-full h-full object-cover opacity-80 transition-opacity duration-500">
        <!-- Deep Gradient Overlays for Readability -->
        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/55 to-black/35"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A] via-transparent to-black/60"></div>
    </div>

    <!-- Container Grid: Left Content (50%) & Right Interactive Slider (50%) -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

            <!-- LEFT SIDE: Dynamic City Info & CTA -->
            <div class="lg:col-span-5 text-white space-y-6">
                <!-- Location & Category Tag -->
                <div class="inline-flex items-center gap-2 text-white/90 text-xs sm:text-sm tracking-wide">
                    <svg class="w-4 h-4 text-[#D32F2F] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    <span id="hero-tagline" class="font-medium tracking-wide">
                        {{ $destinations[0]['tagline'] ?? 'Jawa Tengah & DIY / Budaya' }}
                    </span>
                </div>

                <!-- Big Dynamic City Title -->
                <h1 id="hero-title" class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black uppercase tracking-tight text-white leading-none transition-all duration-300">
                    {{ $destinations[0]['name'] ?? 'SOLO' }}
                </h1>

                <!-- Dynamic City Description -->
                <p id="hero-desc" class="text-gray-300 text-sm sm:text-base leading-relaxed max-w-md transition-all duration-300">
                    {{ $destinations[0]['description'] ?? 'Kota batik dan keraton dengan kuliner khas, pasar tradisional, dan suasana yang tenang.' }}
                </p>

                <!-- Actions: Jelajahi & Lihat Semua -->
                <div class="flex items-center gap-3 pt-2">
                    <a href="#destinasi" class="bg-[#D32F2F] hover:bg-[#B71C1C] text-white text-sm sm:text-base font-semibold px-7 py-3 rounded-full flex items-center gap-2 shadow-lg shadow-red-900/30 transition-all duration-200 group">
                        <span>Jelajahi</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1 duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a href="#layanan" class="border border-white/40 hover:border-white hover:bg-white/10 text-white text-sm sm:text-base font-medium px-6 py-3 rounded-full transition-all duration-200">
                        Lihat Semua
                    </a>
                </div>
            </div>

            <!-- RIGHT SIDE: Destination Card Carousel / Slider -->
            <div class="lg:col-span-7 relative">
                
                <!-- Cards Track Container -->
                <div class="relative overflow-hidden py-4 -mr-4 sm:-mr-6 lg:-mr-12">
                    <div id="destination-slider" class="flex gap-4 overflow-x-auto scrollbar-none scroll-smooth pl-2 pr-12 py-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                        @foreach ($destinations as $index => $dest)
                            <div class="destination-card flex-shrink-0 cursor-pointer transition-all duration-300 select-none group"
                                 data-index="{{ $index }}"
                                 data-id="{{ $dest['id'] }}"
                                 data-name="{{ $dest['name'] }}"
                                 data-tagline="{{ $dest['tagline'] }}"
                                 data-desc="{{ $dest['description'] }}"
                                 data-image="{{ $dest['image'] }}">
                                
                                <div class="card-inner relative w-40 sm:w-44 h-72 sm:h-80 rounded-2xl overflow-hidden border-2 transition-all duration-300 shadow-xl {{ $loop->first ? 'border-white scale-105 shadow-2xl ring-4 ring-white/20' : 'border-transparent opacity-85 hover:opacity-100 hover:scale-102' }}">
                                    <!-- Image / Visual -->
                                    <img src="{{ $dest['image'] }}" alt="{{ $dest['name'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    
                                    <!-- Bottom Gradient for Text -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                                    
                                    <!-- Card Info -->
                                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                        <div class="flex items-center gap-1.5 text-xs text-gray-300 mb-1">
                                            <svg class="w-3.5 h-3.5 text-[#D32F2F]" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="truncate">{{ $dest['province'] }}</span>
                                        </div>
                                        <h3 class="text-lg sm:text-xl font-bold tracking-tight text-white group-hover:text-red-400 transition-colors">
                                            {{ $dest['name'] }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Slider Controls: Prev/Next & Counter -->
                <div class="flex items-center justify-between sm:justify-start gap-4 mt-6 pl-2">
                    <div class="flex items-center gap-2">
                        <button id="slider-prev" type="button" class="w-10 h-10 rounded-full border border-white/30 text-white flex items-center justify-center hover:bg-white hover:text-black hover:border-white transition-all duration-200" aria-label="Destinasi Sebelumnya">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button id="slider-next" type="button" class="w-10 h-10 rounded-full border border-white/30 text-white flex items-center justify-center hover:bg-white hover:text-black hover:border-white transition-all duration-200" aria-label="Destinasi Berikutnya">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Slide Counter (e.g. 01 / 12) -->
                    <div id="slider-counter" class="text-xs sm:text-sm font-medium tracking-widest text-gray-400">
                        <span id="current-slide" class="text-white font-bold">01</span> / {{ str_pad(count($destinations), 2, '0', STR_PAD_LEFT) }}
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>
