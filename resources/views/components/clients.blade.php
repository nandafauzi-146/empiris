@props(['clients' => []])

<section id="klien" class="py-20 lg:py-28 bg-[#FAFAFA]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-14 lg:mb-16">
            <span class="text-[#D32F2F] font-bold text-xs sm:text-sm tracking-widest uppercase block mb-3">
                KLIEN KAMI
            </span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                Dipercaya berbagai instansi dan perusahaan
            </h2>
        </div>

        <!-- Logos Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4 max-w-6xl mx-auto">
            @foreach ($clients as $client)
                <div class="bg-white rounded-xl p-3 sm:p-4 border border-gray-200/70 shadow-xs hover:shadow-md hover:border-gray-300 hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center h-20 sm:h-24 group">
                    <img src="{{ $client['logo'] }}" 
                         alt="{{ $client['name'] }}" 
                         title="{{ $client['name'] }}"
                         class="max-h-12 sm:max-h-14 w-auto object-contain transition-transform duration-200 group-hover:scale-105"
                         loading="lazy">
                </div>
            @endforeach
        </div>

    </div>
</section>
