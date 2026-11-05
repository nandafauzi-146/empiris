@extends('layouts.app')

@section('title', 'Masuk ke Akun - Empirise Jalan-Jalan')

@section('content')
<div class="min-h-screen bg-[#FFF8F6] flex flex-col justify-between pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl w-full mx-auto my-auto">
        
        <!-- White Rounded Split Card -->
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden grid grid-cols-1 md:grid-cols-2 min-h-[580px]">
            
            <!-- Left Banner: Gradient Mesh with Text -->
            <div class="relative p-8 sm:p-12 flex flex-col justify-between overflow-hidden bg-gradient-to-br from-[#E53935] via-[#C62828] to-[#880E4F] text-white">
                <!-- Blurred background ambient blobs -->
                <div class="absolute -top-20 -left-20 w-72 h-72 bg-rose-400 rounded-full mix-blend-screen filter blur-3xl opacity-40"></div>
                <div class="absolute -bottom-20 -right-20 w-72 h-72 bg-amber-400 rounded-full mix-blend-screen filter blur-3xl opacity-30"></div>

                <!-- Top Asterisk / Sparkle Icon -->
                <div class="relative z-10">
                    <svg class="w-8 h-8 text-white/90" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l1.5 7.5L21 11l-7.5 1.5L12 20l-1.5-7.5L3 11l7.5-1.5L12 2z"/>
                    </svg>
                </div>

                <!-- Bottom Text -->
                <div class="relative z-10 mt-28">
                    <p class="text-white/80 text-xs sm:text-sm tracking-wide font-medium mb-2">
                        Selamat datang kembali
                    </p>
                    <h2 class="text-2xl sm:text-3xl font-extrabold leading-tight text-white tracking-tight">
                        Pantau konsultasi dan proyekmu dalam satu tempat
                    </h2>
                </div>
            </div>

            <!-- Right Form Panel -->
            <div class="p-8 sm:p-12 flex flex-col justify-center">
                
                <!-- Red Sparkle Icon -->
                <div class="mb-4">
                    <svg class="w-6 h-6 text-[#D32F2F]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l1.5 7.5L21 11l-7.5 1.5L12 20l-1.5-7.5L3 11l7.5-1.5L12 2z"/>
                    </svg>
                </div>

                <!-- Title & Subtitle -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                    Masuk ke akun
                </h1>
                <p class="text-gray-500 text-xs sm:text-sm mt-1.5 mb-6">
                    Akses konsultasi, proyek, dan pembayaranmu kapan saja.
                </p>

                <!-- Form -->
                <form action="#" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Email</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               placeholder="nama@email.com" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#D32F2F] focus:ring-2 focus:ring-red-100 transition">
                    </div>

                    <!-- Kata Sandi Input with Toggle -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Kata sandi</label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   placeholder="••••••••" 
                                   class="w-full px-4 py-2.5 pr-11 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#D32F2F] focus:ring-2 focus:ring-red-100 transition">
                            <button type="button" 
                                    id="toggle-password" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-[#D32F2F] hover:bg-[#B71C1C] text-white font-semibold py-3 rounded-xl shadow-md transition-all duration-200 mt-2">
                        Masuk
                    </button>
                </form>

                <!-- Divider: Coba Akun Demo -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-3 text-gray-400 font-medium">atau coba akun demo</span>
                    </div>
                </div>

                <!-- Quick Demo Buttons -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <button type="button" 
                            onclick="fillDemo('klien@empirise.id', 'demo1234')"
                            class="w-full py-2 px-3 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 rounded-xl text-xs font-semibold text-gray-700 transition">
                        Klien
                    </button>
                    <button type="button" 
                            onclick="fillDemo('admin@empirise.id', 'admin1234')"
                            class="w-full py-2 px-3 bg-[#111827] hover:bg-black text-white rounded-xl text-xs font-semibold transition">
                        Admin
                    </button>
                </div>

                <!-- Footer Link -->
                <p class="text-center text-xs text-gray-500">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-[#D32F2F] font-bold hover:underline">Daftar</a>
                </p>

            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillDemo(email, pass) {
        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('password');
        if (emailInput && passInput) {
            emailInput.value = email;
            passInput.value = pass;
        }
    }

    const togglePasswordBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        });
    }
</script>
@endpush
