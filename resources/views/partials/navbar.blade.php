<nav id="main-nav"
     class="fixed top-0 left-0 right-0 z-50 bg-white transition-shadow duration-300"
     x-data="{ open: false, scrolled: false }"
     @scroll.window="scrolled = window.scrollY > 10">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <!-- Compass SVG Icon -->
                <div class="w-10 h-10 rounded-full bg-[#0F2044] flex items-center justify-center flex-shrink-0 group-hover:bg-[#C9A84C] transition-colors duration-300">
                    <svg class="w-6 h-6 text-[#C9A84C] group-hover:text-[#0F2044] transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke-width="1.5"/>
                        <polygon points="12,2 15,9 12,12 9,9" fill="currentColor" opacity="0.8"/>
                        <polygon points="12,22 9,15 12,12 15,15" fill="currentColor" opacity="0.4"/>
                        <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                    </svg>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="font-bold text-[#0F2044] text-lg tracking-widest uppercase">EMPIRISE</span>
                    <span class="text-[#C9A84C] text-xs font-medium tracking-wide">Karya Hutama Tour</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center gap-8">
                <a href="{{ route('home') }}"
                   class="text-gray-700 hover:text-[#0F2044] font-medium text-sm tracking-wide transition-colors duration-200 relative group">
                    Beranda
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#C9A84C] group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ route('about') }}"
                   class="text-gray-700 hover:text-[#0F2044] font-medium text-sm tracking-wide transition-colors duration-200 relative group">
                    Tentang Kami
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#C9A84C] group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ route('packages') }}"
                   class="text-gray-700 hover:text-[#0F2044] font-medium text-sm tracking-wide transition-colors duration-200 relative group">
                    Paket Wisata
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#C9A84C] group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#galeri"
                   class="text-gray-700 hover:text-[#0F2044] font-medium text-sm tracking-wide transition-colors duration-200 relative group">
                    Galeri
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#C9A84C] group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ route('contact') }}"
                   class="text-gray-700 hover:text-[#0F2044] font-medium text-sm tracking-wide transition-colors duration-200 relative group">
                    Kontak
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#C9A84C] group-hover:w-full transition-all duration-300"></span>
                </a>
            </div>

            <!-- CTA Button (Desktop) -->
            <div class="hidden lg:block">
                <a href="{{ route('contact') }}"
                   class="inline-block bg-[#C9A84C] text-[#0F2044] font-semibold px-6 py-3 rounded-lg hover:bg-[#b8912a] transition-colors duration-200 text-sm tracking-wide">
                    Pesan Sekarang
                </a>
            </div>

            <!-- Hamburger (Mobile) -->
            <button class="lg:hidden p-2 rounded-lg text-[#0F2044] hover:bg-gray-100 transition-colors"
                    @click="open = !open"
                    aria-label="Toggle menu">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden bg-white border-t border-gray-100 shadow-lg"
         style="display: none;">
        <div class="px-4 py-6 space-y-4">
            <a href="{{ route('home') }}"
               class="block text-gray-700 hover:text-[#0F2044] font-medium py-2 border-b border-gray-100"
               @click="open = false">Beranda</a>
            <a href="{{ route('about') }}"
               class="block text-gray-700 hover:text-[#0F2044] font-medium py-2 border-b border-gray-100"
               @click="open = false">Tentang Kami</a>
            <a href="{{ route('packages') }}"
               class="block text-gray-700 hover:text-[#0F2044] font-medium py-2 border-b border-gray-100"
               @click="open = false">Paket Wisata</a>
            <a href="#galeri"
               class="block text-gray-700 hover:text-[#0F2044] font-medium py-2 border-b border-gray-100"
               @click="open = false">Galeri</a>
            <a href="{{ route('contact') }}"
               class="block text-gray-700 hover:text-[#0F2044] font-medium py-2 border-b border-gray-100"
               @click="open = false">Kontak</a>
            <a href="{{ route('contact') }}"
               class="block text-center bg-[#C9A84C] text-[#0F2044] font-semibold px-6 py-3 rounded-lg hover:bg-[#b8912a] transition-colors mt-4"
               @click="open = false">Pesan Sekarang</a>
        </div>
    </div>
</nav>
