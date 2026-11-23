@props(['testimonials' => []])

<section id="testimoni" class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-14 lg:mb-16">
            <span class="text-[#D32F2F] font-bold text-xs sm:text-sm tracking-widest uppercase block mb-3">
                TESTIMONI TERBARU
            </span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                Apa kata klien kami
            </h2>
        </div>

        <!-- 3 Testimonial Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
            @foreach ($testimonials as $t)
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <!-- 5 Stars -->
                        <div class="flex items-center gap-1 text-[#F59E0B] mb-5">
                            @for ($i = 0; $i < ($t['rating'] ?? 5); $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>

                        <!-- Quote -->
                        <p class="text-gray-700 text-sm sm:text-base leading-relaxed italic mb-6">
                            &ldquo;{{ $t['quote'] }}&rdquo;
                        </p>
                    </div>

                    <!-- Author Details -->
                    <div class="pt-4 border-t border-gray-100">
                        <p class="font-bold text-gray-900 text-sm tracking-tight">
                            {{ $t['author'] }}
                        </p>
                        <p class="text-gray-500 text-xs">
                            {{ $t['company'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
