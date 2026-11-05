@extends('layouts.app')

@section('title', 'Daftar Akun Klien - Empirise Jalan-Jalan')

@section('content')
<div class="min-h-screen bg-[#FFF8F6] flex flex-col justify-between pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl w-full mx-auto my-auto">
        
        <!-- White Rounded Split Card -->
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden grid grid-cols-1 md:grid-cols-2 min-h-[620px]">
            
            <!-- Left Form Panel -->
            <div class="p-8 sm:p-12 flex flex-col justify-center order-2 md:order-1">
                
                <!-- Red Sparkle Icon -->
                <div class="mb-3">
                    <svg class="w-6 h-6 text-[#D32F2F]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l1.5 7.5L21 11l-7.5 1.5L12 20l-1.5-7.5L3 11l7.5-1.5L12 2z"/>
                    </svg>
                </div>

                <!-- Title & Subtitle -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                    Daftar akun klien
                </h1>
                <p class="text-gray-500 text-xs sm:text-sm mt-1 mb-5">
                    Isi data singkat untuk mulai berkonsultasi.
                </p>

                <!-- Form -->
                <form action="#" method="POST" class="space-y-3.5">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nama lengkap</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               placeholder="Nama kamu" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#D32F2F] focus:ring-2 focus:ring-red-100 transition">
                    </div>

                    <!-- 2 Column Row: Instansi & No. WhatsApp -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="company" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Instansi</label>
                            <input type="text" 
                                   id="company" 
                                   name="company" 
                                   placeholder="Opsional" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#D32F2F] focus:ring-2 focus:ring-red-100 transition">
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">No. WhatsApp</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   placeholder="08xx" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#D32F2F] focus:ring-2 focus:ring-red-100 transition">
                        </div>
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label for="reg-email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Email</label>
                        <input type="email" 
                               id="reg-email" 
                               name="email" 
                               placeholder="nama@email.com" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#D32F2F] focus:ring-2 focus:ring-red-100 transition">
                    </div>

                    <!-- Kata Sandi Input with Toggle -->
                    <div>
                        <label for="reg-password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Kata sandi</label>
                        <div class="relative">
                            <input type="password" 
                                   id="reg-password" 
                                   name="password" 
                                   placeholder="••••••••" 
                                   class="w-full px-4 py-2.5 pr-11 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#D32F2F] focus:ring-2 focus:ring-red-100 transition">
                            <button type="button" 
                                    id="toggle-reg-password" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-[#D32F2F] hover:bg-[#B71C1C] text-white font-semibold py-3 rounded-xl shadow-md transition-all duration-200 mt-2">
                        Daftar
                    </button>
                </form>

                <!-- Footer Link -->
                <p class="text-center text-xs text-gray-500 mt-6">
                    Sudah punya akun? <a href="{{ route('login') }}" class="text-[#D32F2F] font-bold hover:underline">Masuk</a>
                </p>

            </div>

            <!-- Right Banner: Gradient Mesh with Text -->
            <div class="relative p-8 sm:p-12 flex flex-col justify-between overflow-hidden bg-gradient-to-bl from-[#E53935] via-[#C62828] to-[#880E4F] text-white order-1 md:order-2">
                <!-- Blurred background ambient blobs -->
                <div class="absolute -top-20 -right-20 w-72 h-72 bg-rose-400 rounded-full mix-blend-screen filter blur-3xl opacity-40"></div>
                <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-amber-400 rounded-full mix-blend-screen filter blur-3xl opacity-30"></div>

                <!-- Top Asterisk / Sparkle Icon -->
                <div class="relative z-10">
                    <svg class="w-8 h-8 text-white/90" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l1.5 7.5L21 11l-7.5 1.5L12 20l-1.5-7.5L3 11l7.5-1.5L12 2z"/>
                    </svg>
                </div>

                <!-- Bottom Text -->
                <div class="relative z-10 mt-28">
                    <p class="text-white/80 text-xs sm:text-sm tracking-wide font-medium mb-2">
                        Mulai dengan mudah
                    </p>
                    <h2 class="text-2xl sm:text-3xl font-extrabold leading-tight text-white tracking-tight">
                        Buat akun dan konsultasikan kebutuhanmu bersama kami
                    </h2>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const toggleRegPassBtn = document.getElementById('toggle-reg-password');
    const regPassInput = document.getElementById('reg-password');
    if (toggleRegPassBtn && regPassInput) {
        toggleRegPassBtn.addEventListener('click', function () {
            const isPassword = regPassInput.getAttribute('type') === 'password';
            regPassInput.setAttribute('type', isPassword ? 'text' : 'password');
        });
    }
</script>
@endpush
