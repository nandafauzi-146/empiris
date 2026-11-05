// Empirise Jalan-Jalan Main Client Script

document.addEventListener('DOMContentLoaded', () => {
    // Floating Navbar Scroll State
    const navContainer = document.getElementById('nav-container');
    const mainHeader = document.getElementById('main-header');

    if (navContainer && mainHeader) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 25) {
                navContainer.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-lg', 'border', 'border-gray-200/80');
                navContainer.classList.remove('bg-transparent');
                mainHeader.classList.remove('py-4');
                mainHeader.classList.add('py-2');
            } else {
                navContainer.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-lg', 'border', 'border-gray-200/80');
                navContainer.classList.add('bg-white/70', 'backdrop-blur-xs');
                mainHeader.classList.remove('py-2');
                mainHeader.classList.add('py-4');
            }
        }, { passive: true });
    }
});
