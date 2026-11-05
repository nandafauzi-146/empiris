@extends('layouts.app')

@section('title', 'Empirise Jalan-Jalan - Liburan Alam & Budaya Premium')

@section('content')

    <!-- Hero Section with Interactive Destination Slider -->
    <x-hero :destinations="$destinations" />

    <!-- Services Section ("LAYANAN") -->
    <x-services :services="$services" />

    <!-- Portfolio Section ("PORTOFOLIO PILIHAN") -->
    <x-portfolio :portfolios="$portfolios" />

    <!-- Client Partners Section ("KLIEN KAMI") -->
    <x-clients :clients="$clients" />

    <!-- Testimonials Section ("TESTIMONI TERBARU") -->
    <x-testimonials :testimonials="$testimonials" />

    <!-- Call to Action Banner -->
    <x-cta-banner />

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        /* ========================================================
           1. FLOATING NAVBAR ON SCROLL (Vanilla JS)
           ======================================================== */
        const mainHeader = document.getElementById('main-header');
        const navContainer = document.getElementById('nav-container');

        function updateNavbarOnScroll() {
            if (window.scrollY > 25) {
                // Floating Dock State
                navContainer.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-lg', 'border', 'border-gray-200/80');
                navContainer.classList.remove('bg-transparent');
                mainHeader.classList.remove('py-4');
                mainHeader.classList.add('py-2');
            } else {
                // Initial Top State
                navContainer.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-lg', 'border', 'border-gray-200/80');
                navContainer.classList.add('bg-white/70', 'backdrop-blur-xs');
                mainHeader.classList.remove('py-2');
                mainHeader.classList.add('py-4');
            }
        }

        window.addEventListener('scroll', updateNavbarOnScroll, { passive: true });
        updateNavbarOnScroll(); // Initial check on load

        /* ========================================================
           2. MOBILE MENU TOGGLE (Vanilla JS)
           ======================================================== */
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const hamburgerIcon = document.getElementById('hamburger-icon');
        const closeIcon = document.getElementById('close-icon');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function () {
                const isOpen = !mobileMenu.classList.contains('hidden');
                if (isOpen) {
                    mobileMenu.classList.add('hidden');
                    hamburgerIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                } else {
                    mobileMenu.classList.remove('hidden');
                    hamburgerIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                }
            });

            // Close on clicking outside
            document.addEventListener('click', function (e) {
                if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target) && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    hamburgerIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            });
        }

        /* ========================================================
           3. INTERACTIVE DESTINATION SLIDER (Vanilla JS)
           ======================================================== */
        const sliderTrack = document.getElementById('destination-slider');
        const cards = document.querySelectorAll('.destination-card');
        const prevBtn = document.getElementById('slider-prev');
        const nextBtn = document.getElementById('slider-next');
        const currentSlideEl = document.getElementById('current-slide');

        const heroTitle = document.getElementById('hero-title');
        const heroTagline = document.getElementById('hero-tagline');
        const heroDesc = document.getElementById('hero-desc');

        let currentIndex = 0;
        const totalSlides = cards.length;

        function setActiveDestination(index, smoothScroll = true) {
            if (index < 0) index = 0;
            if (index >= totalSlides) index = totalSlides - 1;

            currentIndex = index;
            const targetCard = cards[currentIndex];
            if (!targetCard) return;

            // Update Left Side Info
            const name = targetCard.getAttribute('data-name');
            const tagline = targetCard.getAttribute('data-tagline');
            const desc = targetCard.getAttribute('data-desc');

            if (heroTitle) {
                heroTitle.style.opacity = '0';
                setTimeout(() => {
                    heroTitle.textContent = name;
                    heroTitle.style.opacity = '1';
                }, 150);
            }

            if (heroTagline) {
                heroTagline.textContent = tagline;
            }

            if (heroDesc) {
                heroDesc.style.opacity = '0';
                setTimeout(() => {
                    heroDesc.textContent = desc;
                    heroDesc.style.opacity = '1';
                }, 150);
            }

            // Update Counter (01, 02, etc.)
            if (currentSlideEl) {
                currentSlideEl.textContent = String(currentIndex + 1).padStart(2, '0');
            }

            // Update Card Styles
            cards.forEach((c, idx) => {
                const inner = c.querySelector('.card-inner');
                if (inner) {
                    if (idx === currentIndex) {
                        inner.classList.add('border-white', 'scale-105', 'shadow-2xl', 'ring-4', 'ring-white/20');
                        inner.classList.remove('border-transparent', 'opacity-85');
                    } else {
                        inner.classList.remove('border-white', 'scale-105', 'shadow-2xl', 'ring-4', 'ring-white/20');
                        inner.classList.add('border-transparent', 'opacity-85');
                    }
                }
            });

            // Scroll Card into View within Track
            if (smoothScroll && sliderTrack) {
                const cardLeft = targetCard.offsetLeft;
                const trackWidth = sliderTrack.clientWidth;
                const cardWidth = targetCard.clientWidth;
                // Center the active card in the slider viewport
                const targetScroll = cardLeft - (trackWidth / 2) + (cardWidth / 2);
                sliderTrack.scrollTo({
                    left: Math.max(0, targetScroll),
                    behavior: 'smooth'
                });
            }
        }

        // Attach click listener to each destination card
        cards.forEach((card, idx) => {
            card.addEventListener('click', function () {
                setActiveDestination(idx);
            });
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                setActiveDestination(currentIndex > 0 ? currentIndex - 1 : totalSlides - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                setActiveDestination(currentIndex < totalSlides - 1 ? currentIndex + 1 : 0);
            });
        }

        /* ========================================================
           4. PORTOFOLIO CAROUSEL INTERACTIVITY (Vanilla JS)
           ======================================================== */
        const portfolioTrack = document.getElementById('portfolio-container');
        const portfolioItems = document.querySelectorAll('.portfolio-item');
        const portfolioDots = document.querySelectorAll('.portfolio-dot');
        const portfolioPrev = document.getElementById('portfolio-prev');
        const portfolioNext = document.getElementById('portfolio-next');

        let activePortfolioIdx = 2; // Default to Bogor (index 2)

        function setActivePortfolio(index, smoothScroll = true) {
            if (index < 0) index = 0;
            if (index >= portfolioItems.length) index = portfolioItems.length - 1;

            activePortfolioIdx = index;
            const targetItem = portfolioItems[activePortfolioIdx];
            if (!targetItem) return;

            portfolioItems.forEach((item, idx) => {
                const card = item.querySelector('.portfolio-card');
                const cta = item.querySelector('.active-cta');
                if (card) {
                    if (idx === activePortfolioIdx) {
                        card.classList.add('ring-4', 'ring-[#D32F2F]/20', 'scale-105', 'shadow-2xl');
                        card.classList.remove('opacity-80');
                        if (cta) cta.classList.remove('hidden');
                    } else {
                        card.classList.remove('ring-4', 'ring-[#D32F2F]/20', 'scale-105', 'shadow-2xl');
                        card.classList.add('opacity-80');
                        if (cta) cta.classList.add('hidden');
                    }
                }
            });

            // Update Dots
            portfolioDots.forEach((dot, idx) => {
                if (idx === activePortfolioIdx) {
                    dot.classList.add('bg-[#D32F2F]', 'w-7');
                    dot.classList.remove('bg-gray-300');
                } else {
                    dot.classList.remove('bg-[#D32F2F]', 'w-7');
                    dot.classList.add('bg-gray-300');
                }
            });

            // Scroll item into center view
            if (smoothScroll && portfolioTrack) {
                const itemLeft = targetItem.offsetLeft;
                const trackWidth = portfolioTrack.clientWidth;
                const itemWidth = targetItem.clientWidth;
                const targetScroll = itemLeft - (trackWidth / 2) + (itemWidth / 2);
                portfolioTrack.scrollTo({
                    left: Math.max(0, targetScroll),
                    behavior: 'smooth'
                });
            }
        }

        portfolioItems.forEach((item, idx) => {
            item.addEventListener('click', function () {
                setActivePortfolio(idx);
            });
        });

        portfolioDots.forEach((dot, idx) => {
            dot.addEventListener('click', function () {
                setActivePortfolio(idx);
            });
        });

        if (portfolioPrev) {
            portfolioPrev.addEventListener('click', function () {
                setActivePortfolio(activePortfolioIdx > 0 ? activePortfolioIdx - 1 : portfolioItems.length - 1);
            });
        }

        if (portfolioNext) {
            portfolioNext.addEventListener('click', function () {
                setActivePortfolio(activePortfolioIdx < portfolioItems.length - 1 ? activePortfolioIdx + 1 : 0);
            });
        }

    });
</script>
@endpush
