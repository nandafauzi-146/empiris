@props(['services' => []])

<section id="layanan" class="py-20 lg:py-28 bg-[#FAFAFA]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="text-center max-w-3xl mx-auto mb-14 lg:mb-20">
            <span class="text-[#D32F2F] font-bold text-xs sm:text-sm tracking-widest uppercase block mb-3">
                LAYANAN
            </span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                Satu mitra untuk seluruh kebutuhan kegiatan Anda
            </h2>
        </div>

        <!-- 5 Services Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 lg:gap-6">
            @foreach ($services as $service)
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <!-- Icon Box -->
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-[#D32F2F] flex items-center justify-center mb-5 group-hover:bg-[#D32F2F] group-hover:text-white transition-colors duration-300">
                            @if ($service['icon'] === 'compass')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            @elseif ($service['icon'] === 'sparkles')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            @elseif ($service['icon'] === 'mountain')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21l6-13 4 8 2-4 4 9"/>
                                </svg>
                            @elseif ($service['icon'] === 'bus')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM4 6h16a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm0 0V4a2 2 0 012-2h12a2 2 0 012 2v2M4 11h16"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            @endif
                        </div>

                        <!-- Title -->
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-2.5 tracking-tight group-hover:text-[#D32F2F] transition-colors">
                            {{ $service['title'] }}
                        </h3>

                        <!-- Description -->
                        <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                            {{ $service['description'] }}
                        </p>
                    </div>

                    <!-- Subtle bottom arrow or link -->
                    <div class="mt-6 pt-4 border-t border-gray-50 flex items-center text-xs font-semibold text-[#D32F2F] group-hover:translate-x-1 transition-transform">
                        <span>Konsultasi layanan</span>
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
