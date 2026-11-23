<footer id="kontak" class="bg-black text-gray-400 py-10 sm:py-12 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
            
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="inline-block group">
                    <img src="{{ asset('assets/images/logo.svg') }}" alt="Empirise Jalan-Jalan" class="h-8 sm:h-9 w-auto object-contain transition-transform group-hover:scale-105">
                </a>
            </div>

            <!-- Contact Info -->
            <div class="text-xs sm:text-sm text-gray-300 flex flex-wrap items-center justify-center gap-2">
                <a href="mailto:halo@empirise.id" class="hover:text-white transition-colors">halo@empirise.id</a>
                <span class="text-gray-600">/</span>
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">
                    WhatsApp 0812-3456-7890
                </a>
            </div>

            <!-- Copyright -->
            <div class="text-xs text-gray-500">
                &copy; {{ date('Y') }} Empirise. Berizin resmi.
            </div>

        </div>
    </div>
</footer>
