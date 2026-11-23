@props(['active' => 'beranda'])

<header id="main-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 py-3 sm:py-4 px-4 sm:px-6 lg:px-8">
    <div id="nav-container" class="max-w-7xl mx-auto flex items-center justify-between transition-all duration-300 px-4 py-2.5 rounded-full">
        
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2 group flex-shrink-0">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="Empirise Jalan-Jalan" class="h-8 sm:h-9 w-auto object-contain transition-transform group-hover:scale-105 duration-200">
        </a>

        <!-- Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-1 lg:gap-2">
            <a href="{{ route('home') }}" 
               class="px-3.5 py-1.5 rounded-full text-sm font-medium transition-colors duration-200 {{ $active === 'beranda' ? 'text-black font-semibold bg-gray-100/80' : 'text-gray-700 hover:text-black hover:bg-gray-100/50' }}">
                Beranda
            </a>
            <a href="#tentang" 
               class="px-3.5 py-1.5 rounded-full text-sm font-medium text-gray-700 hover:text-black hover:bg-gray-100/50 transition-colors duration-200">
                Tentang
            </a>
            <a href="#layanan" 
               class="px-3.5 py-1.5 rounded-full text-sm font-medium text-gray-700 hover:text-black hover:bg-gray-100/50 transition-colors duration-200">
                Layanan
            </a>
            <a href="#destinasi" 
               class="px-3.5 py-1.5 rounded-full text-sm font-medium text-gray-700 hover:text-black hover:bg-gray-100/50 transition-colors duration-200">
                Destinasi
            </a>
            <a href="#konsultasi" 
               class="px-3.5 py-1.5 rounded-full text-sm font-medium text-gray-700 hover:text-black hover:bg-gray-100/50 transition-colors duration-200">
                Konsultasi
            </a>
            <a href="#kontak" 
               class="px-3.5 py-1.5 rounded-full text-sm font-medium text-gray-700 hover:text-black hover:bg-gray-100/50 transition-colors duration-200">
                FAQ & Kontak
            </a>
        </nav>

        <!-- Right CTA: Daftar & Masuk -->
        <div class="hidden sm:flex items-center gap-3">
            <a href="{{ route('register') }}" class="text-gray-700 hover:text-black text-sm font-medium px-3 py-1.5 transition-colors">
                Daftar
            </a>
            <a href="{{ route('login') }}" class="bg-[#D32F2F] hover:bg-[#B71C1C] text-white text-sm font-medium px-5 py-2 rounded-full shadow-sm hover:shadow transition-all duration-200">
                Masuk
            </a>
        </div>

        <!-- Mobile Hamburger Button -->
        <div class="flex md:hidden items-center gap-2">
            <a href="{{ route('login') }}" class="bg-[#D32F2F] text-white text-xs font-medium px-3.5 py-1.5 rounded-full">
                Masuk
            </a>
            <button id="mobile-menu-btn" type="button" class="p-2 rounded-full text-gray-700 hover:text-black hover:bg-gray-100 focus:outline-none" aria-label="Menu navigasi">
                <svg id="hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobile-menu" class="hidden md:hidden max-w-7xl mx-auto mt-2 bg-white/95 backdrop-blur-md border border-gray-200/80 rounded-2xl shadow-xl p-4 transition-all duration-300">
        <nav class="flex flex-col gap-1">
            <a href="{{ route('home') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-black">Beranda</a>
            <a href="#tentang" class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Tentang</a>
            <a href="#layanan" class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Layanan</a>
            <a href="#destinasi" class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Destinasi</a>
            <a href="#konsultasi" class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Konsultasi</a>
            <a href="#kontak" class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">FAQ & Kontak</a>
            <div class="pt-3 mt-2 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('register') }}" class="text-sm font-medium text-gray-700 px-3 py-2">Belum punya akun? <span class="text-[#D32F2F] font-semibold">Daftar</span></a>
                <a href="{{ route('login') }}" class="bg-[#D32F2F] text-white text-sm font-medium px-5 py-2 rounded-full">Masuk</a>
            </div>
        </nav>
    </div>
</header>
