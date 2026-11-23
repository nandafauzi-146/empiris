const menuButton = document.querySelector('[data-mobile-menu-button]');
const mobileMenu = document.querySelector('#mobile-menu');
const scrollHeader = document.querySelector('[data-scroll-header]');

if (scrollHeader instanceof HTMLElement) {
    let scrollHeaderFrame = 0;

    const updateScrollHeader = () => {
        scrollHeaderFrame = 0;
        scrollHeader.toggleAttribute('data-scrolled', window.scrollY > 24);
    };

    const scheduleScrollHeaderUpdate = () => {
        if (scrollHeaderFrame === 0) {
            scrollHeaderFrame = window.requestAnimationFrame(updateScrollHeader);
        }
    };

    updateScrollHeader();
    window.addEventListener('scroll', scheduleScrollHeaderUpdate, { passive: true });
}

let isAuthTransitionPending = false;

const transitionAuthPage = async (url, expectedPage = null, shouldPushHistory = false) => {
    if (isAuthTransitionPending) {
        return;
    }

    const currentMain = document.querySelector('.auth-main');
    const currentVisual = currentMain?.querySelector('.auth-visual');
    const currentPanel = currentMain?.querySelector('.auth-panel');

    if (
        !(currentMain instanceof HTMLElement) ||
        !(currentVisual instanceof HTMLElement) ||
        !(currentPanel instanceof HTMLElement)
    ) {
        return;
    }

    const previousPositions = new Map([
        ['.auth-visual', currentVisual.getBoundingClientRect()],
        ['.auth-panel', currentPanel.getBoundingClientRect()],
    ]);

    isAuthTransitionPending = true;

    try {
        const response = await fetch(url, {
            headers: { Accept: 'text/html' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error(`Permintaan halaman autentikasi gagal (${response.status}).`);
        }

        const responseDocument = new DOMParser().parseFromString(await response.text(), 'text/html');
        const nextMain = responseDocument.querySelector('.auth-main');
        const nextPage = responseDocument.body.dataset.authPage;
        const nextVisual = nextMain?.querySelector('.auth-visual');
        const nextPanel = nextMain?.querySelector('.auth-panel');

        if (
            !(nextMain instanceof HTMLElement) ||
            !(nextVisual instanceof HTMLElement) ||
            !(nextPanel instanceof HTMLElement) ||
            (nextPage !== 'login' && nextPage !== 'register') ||
            (expectedPage !== null && nextPage !== expectedPage)
        ) {
            throw new Error('Respons tidak berisi halaman autentikasi yang sesuai.');
        }

        currentMain.replaceWith(nextMain);
        document.body.dataset.authPage = nextPage;
        document.title = responseDocument.title;

        const nextDescription = responseDocument.querySelector('meta[name="description"]');
        const currentDescription = document.querySelector('meta[name="description"]');

        if (nextDescription instanceof HTMLMetaElement && currentDescription instanceof HTMLMetaElement) {
            currentDescription.content = nextDescription.content;
        }

        if (shouldPushHistory) {
            window.history.pushState({ authPage: nextPage }, '', response.url || url);
        }

        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const card = nextMain.querySelector('.auth-card');
            const fallbackDistance = card instanceof HTMLElement ? card.getBoundingClientRect().width : 0;

            nextMain.querySelectorAll('.auth-visual, .auth-panel').forEach((panel) => {
                const selector = panel.matches('.auth-visual') ? '.auth-visual' : '.auth-panel';
                const previousPosition = previousPositions.get(selector);

                if (!(panel instanceof HTMLElement) || !previousPosition) {
                    return;
                }

                const nextPosition = panel.getBoundingClientRect();
                const offsetX =
                    previousPosition.left +
                    previousPosition.width / 2 -
                    nextPosition.left -
                    nextPosition.width / 2;
                const movesRight =
                    (nextPage === 'register' && selector === '.auth-panel') ||
                    (nextPage === 'login' && selector === '.auth-visual');
                const startingOffsetX =
                    Math.abs(offsetX) > 20
                        ? offsetX
                        : (movesRight ? 1 : -1) * fallbackDistance;

                panel.animate(
                    [
                        { transform: `translateX(${startingOffsetX}px)` },
                        { transform: 'translateX(0)' },
                    ],
                    {
                        duration: 720,
                        easing: 'cubic-bezier(0.65, 0, 0.35, 1)',
                    },
                );
            });
        }
    } catch (error) {
        console.error('Tidak dapat mengganti halaman autentikasi tanpa reload.', error);

        if (shouldPushHistory) {
            window.location.assign(url);
        } else {
            window.location.reload();
        }
    } finally {
        isAuthTransitionPending = false;
    }
};

document.addEventListener('click', (event) => {
    const target = event.target;

    if (!(target instanceof Element)) {
        return;
    }

    const link = target.closest('a[data-auth-transition]');

    if (
        !(link instanceof HTMLAnchorElement) ||
        event.defaultPrevented ||
        event.button !== 0 ||
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey ||
        link.target === '_blank' ||
        link.hasAttribute('download') ||
        link.origin !== window.location.origin ||
        !document.body.classList.contains('auth-layout')
    ) {
        return;
    }

    const targetPage = link.dataset.authTransition;

    if (targetPage !== 'login' && targetPage !== 'register') {
        return;
    }

    event.preventDefault();

    if (isAuthTransitionPending || targetPage === document.body.dataset.authPage) {
        return;
    }

    void transitionAuthPage(link.href, targetPage, true);
});

document.addEventListener('click', (event) => {
    const target = event.target;

    if (!(target instanceof Element)) {
        return;
    }

    const toggle = target.closest('[data-password-toggle]');

    if (!(toggle instanceof HTMLButtonElement)) {
        return;
    }

    const passwordInput = document.getElementById(toggle.dataset.passwordToggle ?? '');

    if (!(passwordInput instanceof HTMLInputElement)) {
        return;
    }

    const shouldShowPassword = passwordInput.type === 'password';

    passwordInput.type = shouldShowPassword ? 'text' : 'password';
    toggle.setAttribute('aria-pressed', String(shouldShowPassword));
    toggle.setAttribute(
        'aria-label',
        shouldShowPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi',
    );
});

window.addEventListener('popstate', () => {
    if (document.body.classList.contains('auth-layout')) {
        void transitionAuthPage(window.location.href);
    }
});

if (menuButton instanceof HTMLButtonElement && mobileMenu instanceof HTMLElement) {
    const setMenuOpen = (isOpen) => {
        menuButton.setAttribute('aria-expanded', String(isOpen));
        menuButton.setAttribute('aria-label', isOpen ? 'Tutup navigasi' : 'Buka navigasi');
        mobileMenu.classList.toggle('hidden', !isOpen);
    };

    menuButton.addEventListener('click', () => {
        setMenuOpen(menuButton.getAttribute('aria-expanded') !== 'true');
    });

    mobileMenu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setMenuOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setMenuOpen(false);
        }
    });
}

const consultationService = document.querySelector('#consultation-service');
const rentalDetails = document.querySelector('[data-rental-details]');

if (consultationService instanceof HTMLSelectElement && rentalDetails instanceof HTMLFieldSetElement) {
    const rentalInputs = Array.from(rentalDetails.querySelectorAll('input, select'));

    const updateRentalDetails = () => {
        const isRentalService = consultationService.value === 'Rental Armada';

        rentalDetails.hidden = !isRentalService;
        rentalDetails.disabled = !isRentalService;
        rentalInputs.forEach((input) => {
            input.required = isRentalService && input.hasAttribute('data-rental-required');
        });
    };

    consultationService.addEventListener('change', updateRentalDetails);
    updateRentalDetails();
}

const adminSidebar = document.querySelector('[data-admin-sidebar]');
const adminSidebarOverlay = document.querySelector('[data-admin-sidebar-overlay]');
const adminSidebarOpenButton = document.querySelector('[data-admin-sidebar-open]');
const adminSidebarCloseButton = document.querySelector('[data-admin-sidebar-close]');
const adminSidebarCollapseButton = document.querySelector('[data-admin-sidebar-collapse]');
const sidebarAnimationFinishes = new WeakMap();

const animateSidebarResize = (sidebar, animationClass, propertyName, updateSidebar) => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        updateSidebar();
        return;
    }

    sidebarAnimationFinishes.get(sidebar)?.();

    sidebar.classList.add(animationClass);
    void sidebar.offsetWidth;

    const finishAnimation = (event) => {
        if (event && (event.target !== sidebar || event.propertyName !== propertyName)) {
            return;
        }

        sidebar.classList.remove(animationClass);
        sidebar.removeEventListener('transitionend', finishAnimation);
        window.clearTimeout(animationTimeout);
        sidebarAnimationFinishes.delete(sidebar);
    };

    const animationTimeout = window.setTimeout(() => finishAnimation(), 300);
    sidebarAnimationFinishes.set(sidebar, () => finishAnimation());

    sidebar.addEventListener('transitionend', finishAnimation);
    updateSidebar();
};

if (
    adminSidebar instanceof HTMLElement &&
    adminSidebarOverlay instanceof HTMLElement &&
    adminSidebarOpenButton instanceof HTMLButtonElement &&
    adminSidebarCloseButton instanceof HTMLButtonElement &&
    adminSidebarCollapseButton instanceof HTMLButtonElement
) {
    const compactSidebarMedia = window.matchMedia('(max-width: 767px)');
    const sidebarDrawerMedia = window.matchMedia('(max-width: 767px)');
    const sidebarPreferenceKey = 'empirise-admin-sidebar-collapsed';
    const storedSidebarPreference = window.sessionStorage.getItem(sidebarPreferenceKey);
    let hasManualSidebarPreference =
        storedSidebarPreference === 'true' || storedSidebarPreference === 'false';
    let isCompactSidebar = hasManualSidebarPreference
        ? storedSidebarPreference === 'true'
        : compactSidebarMedia.matches;
    let isMobileDrawer = sidebarDrawerMedia.matches;

    const setSidebarCollapsed = (isCollapsed, shouldRemember = false) => {
        adminSidebar.classList.toggle('admin-sidebar-collapsed', isCollapsed);
        adminSidebarCollapseButton.setAttribute('aria-expanded', String(!isCollapsed));
        adminSidebarCollapseButton.setAttribute(
            'aria-label',
            isCollapsed ? 'Perluas sidebar' : 'Ciutkan sidebar',
        );
        adminSidebarCollapseButton.title = isCollapsed ? 'Perluas sidebar' : 'Ciutkan sidebar';
        const collapseIcon = adminSidebarCollapseButton.querySelector('[data-collapse-icon]');
        const expandIcon = adminSidebarCollapseButton.querySelector('[data-expand-icon]');
        collapseIcon?.classList.toggle('hidden', isCollapsed);
        expandIcon?.classList.toggle('hidden', !isCollapsed);

        if (shouldRemember) {
            window.sessionStorage.setItem(sidebarPreferenceKey, String(isCollapsed));
            hasManualSidebarPreference = true;
        }
    };

    const setSidebarDrawerOpen = (isOpen) => {
        const shouldOpen = isOpen && sidebarDrawerMedia.matches;

        if (sidebarDrawerMedia.matches) {
            adminSidebar.classList.toggle('hidden', !shouldOpen);
        } else {
            adminSidebar.classList.remove('hidden');
        }

        adminSidebarOverlay.classList.toggle('hidden', !shouldOpen);
        adminSidebarOverlay.setAttribute('aria-hidden', String(!shouldOpen));
        adminSidebar.setAttribute('aria-hidden', String(!shouldOpen && sidebarDrawerMedia.matches));
        adminSidebar.toggleAttribute('inert', !shouldOpen && sidebarDrawerMedia.matches);
        adminSidebarOpenButton.setAttribute('aria-expanded', String(shouldOpen));

        if (shouldOpen) {
            adminSidebar.querySelector('nav a')?.focus();
        } else if (sidebarDrawerMedia.matches && adminSidebar.contains(document.activeElement)) {
            adminSidebarOpenButton.focus();
        }
    };

    setSidebarCollapsed(isCompactSidebar);
    document.documentElement.removeAttribute('data-admin-sidebar-initially-collapsed');
    setSidebarDrawerOpen(false);

    window.addEventListener('resize', () => {
        if (compactSidebarMedia.matches !== isCompactSidebar) {
            isCompactSidebar = compactSidebarMedia.matches;

            if (!hasManualSidebarPreference) {
                setSidebarCollapsed(isCompactSidebar);
            }
        }

        if (sidebarDrawerMedia.matches !== isMobileDrawer) {
            isMobileDrawer = sidebarDrawerMedia.matches;
            setSidebarDrawerOpen(false);
        }
    });

    adminSidebarCollapseButton.addEventListener('click', () => {
        const isCollapsed = !adminSidebar.classList.contains('admin-sidebar-collapsed');

        if (!sidebarDrawerMedia.matches) {
            animateSidebarResize(
                adminSidebar,
                'admin-sidebar-motion-enabled',
                'width',
                () => setSidebarCollapsed(isCollapsed, true),
            );
        } else {
            setSidebarCollapsed(isCollapsed, true);
        }
    });

    adminSidebarOpenButton.addEventListener('click', () => {
        setSidebarDrawerOpen(true);
    });

    adminSidebarCloseButton.addEventListener('click', () => {
        setSidebarDrawerOpen(false);
    });

    adminSidebarOverlay.addEventListener('click', () => {
        setSidebarDrawerOpen(false);
    });

    adminSidebar.querySelectorAll('nav a').forEach((link) => {
        link.addEventListener('click', () => setSidebarDrawerOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setSidebarDrawerOpen(false);
        }
    });
}

const accountShell = document.querySelector('[data-account-shell]');
const accountSidebarCollapseButton = document.querySelector('[data-account-sidebar-collapse]');

if (accountShell instanceof HTMLElement && accountSidebarCollapseButton instanceof HTMLButtonElement) {
    const compactAccountSidebarMedia = window.matchMedia('(min-width: 48rem) and (max-width: 63.999rem)');
    const sidebarPreferenceKey = 'empirise-account-sidebar-collapsed';
    const storedSidebarPreference = window.sessionStorage.getItem(sidebarPreferenceKey);
    let hasManualSidebarPreference =
        storedSidebarPreference === 'true' || storedSidebarPreference === 'false';
    let isCompactAccountSidebar = hasManualSidebarPreference
        ? storedSidebarPreference === 'true'
        : compactAccountSidebarMedia.matches;

    const setAccountSidebarCollapsed = (isCollapsed, shouldRemember = false) => {
        accountShell.classList.toggle('account-shell-collapsed', isCollapsed);
        accountSidebarCollapseButton.setAttribute('aria-expanded', String(!isCollapsed));
        accountSidebarCollapseButton.setAttribute(
            'aria-label',
            isCollapsed ? 'Perluas navigasi' : 'Ciutkan navigasi',
        );
        accountSidebarCollapseButton.title = isCollapsed ? 'Perluas navigasi' : 'Ciutkan navigasi';

        if (shouldRemember) {
            window.sessionStorage.setItem(sidebarPreferenceKey, String(isCollapsed));
            hasManualSidebarPreference = true;
        }
    };

    setAccountSidebarCollapsed(isCompactAccountSidebar);
    document.documentElement.removeAttribute('data-account-sidebar-initially-collapsed');

    window.addEventListener('resize', () => {
        if (compactAccountSidebarMedia.matches !== isCompactAccountSidebar) {
            isCompactAccountSidebar = compactAccountSidebarMedia.matches;

            if (!hasManualSidebarPreference) {
                setAccountSidebarCollapsed(isCompactAccountSidebar);
            }
        }
    });

    accountSidebarCollapseButton.addEventListener('click', () => {
        const isCollapsed = !accountShell.classList.contains('account-shell-collapsed');

        if (window.matchMedia('(min-width: 48rem)').matches) {
            animateSidebarResize(
                accountShell,
                'account-shell-motion-enabled',
                'grid-template-columns',
                () => setAccountSidebarCollapsed(isCollapsed, true),
            );
        } else {
            setAccountSidebarCollapsed(isCollapsed, true);
        }
    });
}

const destinationTrack = document.querySelector('[data-destination-track]');
const destinationCards = destinationTrack
    ? Array.from(destinationTrack.querySelectorAll('[data-destination-card]'))
    : [];
const hero = document.querySelector('[data-hero]');
const heroTitle = document.querySelector('[data-hero-title]');
const heroRegion = document.querySelector('[data-hero-region]');
const heroCategory = document.querySelector('[data-hero-category]');
const heroDescription = document.querySelector('[data-hero-description]');
const destinationCount = document.querySelector('[data-destination-count]');
const destinationProgress = document.querySelector('[data-destination-progress]');
const destinationAutoplayToggle = document.querySelector('[data-destination-autoplay]');
const heroImage = hero?.querySelector('[data-hero-image]');
const nextHeroImage = hero?.querySelector('[data-hero-image-next]');

if (
    destinationTrack instanceof HTMLElement &&
    destinationCards.length > 0 &&
    hero instanceof HTMLElement &&
    heroTitle instanceof HTMLElement &&
    heroRegion instanceof HTMLElement &&
    heroCategory instanceof HTMLElement &&
    heroDescription instanceof HTMLElement &&
    destinationCount instanceof HTMLElement &&
    destinationProgress instanceof HTMLElement &&
    destinationAutoplayToggle instanceof HTMLButtonElement &&
    heroImage instanceof HTMLImageElement &&
    nextHeroImage instanceof HTMLImageElement
) {
    const progressBar = destinationProgress.querySelector('span');
    let activeIndex = Math.max(
        0,
        destinationCards.findIndex((card) => card.hasAttribute('data-featured')),
    );
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const autoplayDelay = 5000;
    let activeHeroImage = heroImage;
    let inactiveHeroImage = nextHeroImage;
    let autoplayTimer;
    let isUserPaused = false;
    let hasHeroFocus = false;
    let finishBackgroundTransition = null;
    let backgroundRequestId = 0;
    let destinationScrollFrame = null;
    let destinationScrollTimer = null;

    const updateHeroBackground = (imageSource, direction, shouldAnimate) => {
        const requestId = ++backgroundRequestId;

        finishBackgroundTransition?.();

        if (activeHeroImage.currentSrc === imageSource || activeHeroImage.src === imageSource) {
            return;
        }

        if (!shouldAnimate || prefersReducedMotion.matches) {
            activeHeroImage.src = imageSource;
            inactiveHeroImage.src = imageSource;
            inactiveHeroImage.style.visibility = 'hidden';
            activeHeroImage.style.transform = '';
            activeHeroImage.style.opacity = '';
            inactiveHeroImage.style.transform = '';
            inactiveHeroImage.style.opacity = '';

            return;
        }

        inactiveHeroImage.src = imageSource;
        inactiveHeroImage.style.visibility = 'hidden';
        inactiveHeroImage.style.transform = '';
        inactiveHeroImage.style.opacity = '';

        const startTransition = () => {
            if (requestId !== backgroundRequestId) {
                return;
            }

            inactiveHeroImage.style.visibility = 'visible';

            const outgoingImage = activeHeroImage;
            const incomingImage = inactiveHeroImage;
            const easing = 'cubic-bezier(0.65, 0, 0.35, 1)';
            let isFinished = false;

            const finish = () => {
                if (isFinished) {
                    return;
                }

                isFinished = true;
                outgoingImage.src = imageSource;
                outgoingImage.style.transform = '';
                outgoingImage.style.opacity = '';
                outgoingImage.style.visibility = 'hidden';
                incomingImage.style.transform = '';
                incomingImage.style.opacity = '';
                activeHeroImage = incomingImage;
                inactiveHeroImage = outgoingImage;
                finishBackgroundTransition = null;
            };

            const incomingAnimation = incomingImage.animate(
                [
                    { transform: `translateX(${direction * 18}%)`, opacity: 0.15 },
                    { transform: 'translateX(0)', opacity: 1 },
                ],
                { duration: 900, easing, fill: 'forwards' },
            );
            const outgoingAnimation = outgoingImage.animate(
                [
                    { transform: 'translateX(0)', opacity: 1 },
                    { transform: `translateX(${-direction * 18}%)`, opacity: 0.15 },
                ],
                { duration: 900, easing, fill: 'forwards' },
            );

            finishBackgroundTransition = finish;
            Promise.allSettled([incomingAnimation.finished, outgoingAnimation.finished]).then(finish);
        };

        if (typeof inactiveHeroImage.decode === 'function') {
            inactiveHeroImage.decode().then(startTransition).catch((error) => {
                console.error('Foto destinasi tidak dapat dimuat untuk hero.', error);
                if (activeHeroImage === heroImage || activeHeroImage === nextHeroImage) {
                    activeHeroImage.src = imageSource;
                }
            });
        } else {
            inactiveHeroImage.addEventListener('load', startTransition, { once: true });
        }
    };

    const scrollDestinationCardIntoView = (selectedCard) => {
        window.clearTimeout(destinationScrollTimer);

        if (destinationScrollFrame !== null) {
            window.cancelAnimationFrame(destinationScrollFrame);
        }

        const startScroll = () => {
            if (prefersReducedMotion.matches) {
                const cardRect = selectedCard.getBoundingClientRect();
                const trackRect = destinationTrack.getBoundingClientRect();
                const targetLeft = destinationTrack.scrollLeft
                    + cardRect.left
                    - trackRect.left
                    - ((trackRect.width - cardRect.width) / 2);

                destinationTrack.scrollLeft = Math.max(
                    0,
                    Math.min(targetLeft, destinationTrack.scrollWidth - destinationTrack.clientWidth),
                );

                return;
            }

            destinationTrack.style.scrollSnapType = 'none';
            const startLeft = destinationTrack.scrollLeft;
            const duration = 650;
            const startTime = performance.now();

            const animateScroll = (currentTime) => {
                const elapsed = Math.min((currentTime - startTime) / duration, 1);
                const eased = 1 - Math.pow(1 - elapsed, 3);
                const cardRect = selectedCard.getBoundingClientRect();
                const trackRect = destinationTrack.getBoundingClientRect();
                const targetLeft = destinationTrack.scrollLeft
                    + cardRect.left
                    - trackRect.left
                    - ((trackRect.width - cardRect.width) / 2);
                const clampedTarget = Math.max(
                    0,
                    Math.min(targetLeft, destinationTrack.scrollWidth - destinationTrack.clientWidth),
                );

                destinationTrack.scrollLeft = startLeft + ((clampedTarget - startLeft) * eased);

                if (elapsed < 1) {
                    destinationScrollFrame = window.requestAnimationFrame(animateScroll);
                } else {
                    destinationScrollFrame = null;
                    destinationTrack.style.scrollSnapType = '';
                }
            };

            destinationScrollFrame = window.requestAnimationFrame(animateScroll);
        };

        destinationScrollTimer = window.setTimeout(startScroll, prefersReducedMotion.matches ? 0 : 180);
    };

    const scheduleAutoplay = () => {
        window.clearTimeout(autoplayTimer);

        if (
            destinationCards.length < 2 ||
            isUserPaused ||
            hasHeroFocus ||
            document.hidden ||
            prefersReducedMotion.matches
        ) {
            return;
        }

        autoplayTimer = window.setTimeout(() => {
            selectDestination(activeIndex + 1, true, 1);
        }, autoplayDelay);
    };

    const selectDestination = (index, shouldScroll = true, direction = 1, shouldAnimate = true) => {
        const previousIndex = activeIndex;
        activeIndex = (index + destinationCards.length) % destinationCards.length;
        const selectedCard = destinationCards[activeIndex];
        const image = selectedCard.querySelector('img');

        if (!(image instanceof HTMLImageElement) || !image.src) {
            console.error(`Foto destinasi ${selectedCard.dataset.name ?? ''} tidak tersedia.`);

            return;
        }

        destinationCards.forEach((card, cardIndex) => {
            const isSelected = cardIndex === activeIndex;

            card.classList.toggle('destination-card-featured', isSelected);
            card.setAttribute('aria-pressed', String(isSelected));
            card.toggleAttribute('data-featured', isSelected);
            card.toggleAttribute('aria-current', isSelected);
            if (isSelected) {
                card.setAttribute('aria-current', 'true');
            }
        });

        heroTitle.textContent = selectedCard.dataset.name ?? '';
        heroRegion.textContent = selectedCard.dataset.region ?? '';
        heroCategory.textContent = selectedCard.dataset.category ?? '';
        heroDescription.textContent = selectedCard.dataset.description ?? '';

        updateHeroBackground(image.src, direction, shouldAnimate && previousIndex !== activeIndex);

        destinationCount.textContent = `${String(activeIndex + 1).padStart(2, '0')} / ${String(destinationCards.length).padStart(2, '0')}`;
        destinationProgress.setAttribute('aria-valuenow', String(activeIndex + 1));

        if (progressBar instanceof HTMLElement) {
            progressBar.style.width = `${((activeIndex + 1) / destinationCards.length) * 100}%`;
        }

        if (shouldScroll) {
            scrollDestinationCardIntoView(selectedCard);
        }

        scheduleAutoplay();
    };

    destinationCards.forEach((card, index) => {
        card.addEventListener('click', () => {
            const forwardDistance = (index - activeIndex + destinationCards.length) % destinationCards.length;
            const backwardDistance = (activeIndex - index + destinationCards.length) % destinationCards.length;

            selectDestination(index, true, forwardDistance <= backwardDistance ? 1 : -1);
        });
    });

    document.querySelector('[data-destination-prev]')?.addEventListener('click', () => {
        selectDestination(activeIndex - 1, true, -1);
    });

    document.querySelector('[data-destination-next]')?.addEventListener('click', () => {
        selectDestination(activeIndex + 1, true, 1);
    });

    destinationAutoplayToggle.addEventListener('click', () => {
        isUserPaused = !isUserPaused;
        destinationAutoplayToggle.setAttribute('aria-pressed', String(isUserPaused));
        destinationAutoplayToggle.classList.toggle('is-paused', isUserPaused);
        destinationAutoplayToggle.setAttribute(
            'aria-label',
            isUserPaused ? 'Mulai slide otomatis' : 'Jeda slide otomatis',
        );
        destinationCount.setAttribute('aria-live', isUserPaused ? 'polite' : 'off');

        if (!isUserPaused) {
            hasHeroFocus = false;
        }

        scheduleAutoplay();
    });

    hero.addEventListener('focusin', () => {
        hasHeroFocus = true;
        scheduleAutoplay();
    });
    hero.addEventListener('focusout', (event) => {
        if (!(event.relatedTarget instanceof Node) || !hero.contains(event.relatedTarget)) {
            hasHeroFocus = false;
            scheduleAutoplay();
        }
    });
    document.addEventListener('visibilitychange', scheduleAutoplay);
    prefersReducedMotion.addEventListener('change', () => {
        if (prefersReducedMotion.matches) {
            finishBackgroundTransition?.();
            destinationAutoplayToggle.disabled = true;
            destinationAutoplayToggle.classList.add('is-paused');
            destinationAutoplayToggle.setAttribute('aria-pressed', 'true');
            destinationAutoplayToggle.setAttribute(
                'aria-label',
                'Slide otomatis nonaktif karena preferensi gerakan',
            );
            destinationCount.setAttribute('aria-live', 'polite');
        } else {
            destinationAutoplayToggle.disabled = false;
            destinationAutoplayToggle.classList.toggle('is-paused', isUserPaused);
            destinationAutoplayToggle.setAttribute('aria-pressed', String(isUserPaused));
            destinationAutoplayToggle.setAttribute(
                'aria-label',
                isUserPaused ? 'Mulai slide otomatis' : 'Jeda slide otomatis',
            );
            destinationCount.setAttribute('aria-live', isUserPaused ? 'polite' : 'off');
        }

        scheduleAutoplay();
    });

    if (prefersReducedMotion.matches) {
        destinationAutoplayToggle.disabled = true;
        destinationAutoplayToggle.classList.add('is-paused');
        destinationAutoplayToggle.setAttribute('aria-pressed', 'true');
        destinationAutoplayToggle.setAttribute(
            'aria-label',
            'Slide otomatis nonaktif karena preferensi gerakan',
        );
        destinationCount.setAttribute('aria-live', 'polite');
    }

    selectDestination(activeIndex, true, 1, false);
}

const destinationSearch = document.querySelector('[data-destination-search]');
const destinationRegionFilter = document.querySelector('[data-destination-region]');
const destinationCategoryFilter = document.querySelector('[data-destination-category]');
const destinationListings = Array.from(document.querySelectorAll('[data-destination-listing]'));
const destinationEmptyState = document.querySelector('[data-destination-empty]');

if (
    destinationSearch instanceof HTMLInputElement &&
    destinationRegionFilter instanceof HTMLSelectElement &&
    destinationCategoryFilter instanceof HTMLSelectElement &&
    destinationListings.length > 0 &&
    destinationEmptyState instanceof HTMLElement
) {
    const filterDestinations = () => {
        const searchTerm = destinationSearch.value.trim().toLowerCase();
        let visibleCount = 0;

        destinationListings.forEach((listing) => {
            const matchesSearch = (listing.dataset.name ?? '').toLowerCase().includes(searchTerm);
            const matchesRegion =
                destinationRegionFilter.value === '' ||
                listing.dataset.region === destinationRegionFilter.value;
            const matchesCategory =
                destinationCategoryFilter.value === '' ||
                listing.dataset.category === destinationCategoryFilter.value;
            const isVisible = matchesSearch && matchesRegion && matchesCategory;

            listing.classList.toggle('hidden', !isVisible);
            visibleCount += Number(isVisible);
        });

        destinationEmptyState.classList.toggle('hidden', visibleCount > 0);
    };

    destinationSearch.addEventListener('input', filterDestinations);
    destinationRegionFilter.addEventListener('change', filterDestinations);
    destinationCategoryFilter.addEventListener('change', filterDestinations);
}

const portfolioCategoryFilter = document.querySelector('[data-portfolio-category]');
const portfolioYearFilter = document.querySelector('[data-portfolio-year]');
const portfolioListings = Array.from(document.querySelectorAll('[data-portfolio-listing]'));
const portfolioEmptyState = document.querySelector('[data-portfolio-empty]');

if (
    portfolioCategoryFilter instanceof HTMLSelectElement &&
    portfolioYearFilter instanceof HTMLSelectElement &&
    portfolioListings.length > 0 &&
    portfolioEmptyState instanceof HTMLElement
) {
    const filterPortfolios = () => {
        let visibleCount = 0;

        portfolioListings.forEach((listing) => {
            const matchesCategory =
                portfolioCategoryFilter.value === '' ||
                listing.dataset.category === portfolioCategoryFilter.value;
            const matchesYear =
                portfolioYearFilter.value === '' || listing.dataset.year === portfolioYearFilter.value;
            const isVisible = matchesCategory && matchesYear;

            listing.classList.toggle('hidden', !isVisible);
            visibleCount += Number(isVisible);
        });

        portfolioEmptyState.classList.toggle('hidden', visibleCount > 0);
    };

    portfolioCategoryFilter.addEventListener('change', filterPortfolios);
    portfolioYearFilter.addEventListener('change', filterPortfolios);
}

const portfolioCarousel = document.querySelector('[data-portfolio-carousel]');

if (portfolioCarousel instanceof HTMLElement) {
    const portfolioSlides = Array.from(portfolioCarousel.querySelectorAll('[data-portfolio-slide]'));
    const portfolioDots = Array.from(portfolioCarousel.querySelectorAll('[data-portfolio-dot]'));
    const portfolioPrevious = portfolioCarousel.querySelector('[data-portfolio-prev]');
    const portfolioNext = portfolioCarousel.querySelector('[data-portfolio-next]');

    if (
        portfolioSlides.length > 0 &&
        portfolioSlides.every((slide) => slide instanceof HTMLElement) &&
        portfolioDots.length > 0 &&
        portfolioPrevious instanceof HTMLButtonElement &&
        portfolioNext instanceof HTMLButtonElement
    ) {
        let activePortfolioSlideIndex = Number(portfolioCarousel.dataset.activeIndex ?? 0);
        const portfolioCount = portfolioDots.length;
        let portfolioRebaseTimer = 0;

        const updatePortfolioCarousel = () => {
            portfolioSlides.forEach((slide, index) => {
                const isActive = index === activePortfolioSlideIndex;

                slide.classList.toggle('is-active', isActive);
                slide.setAttribute('aria-hidden', String(!isActive));
                slide.querySelectorAll('a, button').forEach((control) => {
                    if (isActive) {
                        control.removeAttribute('tabindex');
                    } else {
                        control.setAttribute('tabindex', '-1');
                    }
                });
            });

            const activeSlide = portfolioSlides[activePortfolioSlideIndex];
            const activeWidth = activeSlide.getBoundingClientRect().width;
            const sideWidth =
                portfolioSlides.find((_, index) => index !== activePortfolioSlideIndex)?.getBoundingClientRect()
                    .width ?? activeWidth;
            const gap = Number.parseFloat(
                getComputedStyle(portfolioCarousel).getPropertyValue('--portfolio-gap'),
            );
            const activeLogicalIndex = Number(activeSlide.dataset.index);

            portfolioSlides.forEach((slide, index) => {
                const offset = index - activePortfolioSlideIndex;
                const distance = Math.abs(offset);
                const x = distance === 0
                    ? 0
                    : Math.sign(offset) *
                      (activeWidth / 2 + sideWidth / 2 + gap + (distance - 1) * (sideWidth + gap));

                slide.style.setProperty('--portfolio-offset', `${x}px`);
            });

            portfolioDots.forEach((dot, index) => {
                dot.classList.toggle('is-active', index === activeLogicalIndex);
                dot.setAttribute('aria-pressed', String(index === activeLogicalIndex));
            });
        };

        const rebasePortfolioCarousel = () => {
            window.clearTimeout(portfolioRebaseTimer);
            portfolioRebaseTimer = 0;

            const activeSlide = portfolioSlides[activePortfolioSlideIndex];
            const activeCopy = Number(activeSlide.dataset.copy);

            if (activeCopy === 1) {
                return;
            }

            portfolioCarousel.classList.add('is-rebasing');
            activePortfolioSlideIndex = portfolioCount + Number(activeSlide.dataset.index);
            updatePortfolioCarousel();
            window.requestAnimationFrame(() => {
                window.requestAnimationFrame(() => {
                    portfolioCarousel.classList.remove('is-rebasing');
                });
            });
        };

        const findClosestPortfolioSlide = (logicalIndex) =>
            portfolioSlides
                .map((slide, slideIndex) => ({ slide, slideIndex }))
                .filter(({ slide }) => Number(slide.dataset.index) === logicalIndex)
                .reduce((nearest, current) =>
                    Math.abs(current.slideIndex - activePortfolioSlideIndex) <
                    Math.abs(nearest.slideIndex - activePortfolioSlideIndex)
                        ? current
                        : nearest,
                );

        const selectPortfolio = (index) => {
            activePortfolioSlideIndex = Math.max(0, Math.min(index, portfolioSlides.length - 1));
            updatePortfolioCarousel();

            if (Number(portfolioSlides[activePortfolioSlideIndex].dataset.copy) !== 1) {
                portfolioRebaseTimer = window.setTimeout(rebasePortfolioCarousel, 650);
            }
        };

        portfolioPrevious.addEventListener('click', () => {
            selectPortfolio(activePortfolioSlideIndex - 1);
        });

        portfolioNext.addEventListener('click', () => {
            selectPortfolio(activePortfolioSlideIndex + 1);
        });

        portfolioDots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                selectPortfolio(findClosestPortfolioSlide(index).slideIndex);
            });
        });

        portfolioSlides.forEach((slide) => {
            slide.addEventListener('click', (event) => {
                if (event.target instanceof Element && event.target.closest('a, button')) {
                    return;
                }

                const logicalIndex = Number(slide.dataset.index);

                selectPortfolio(findClosestPortfolioSlide(logicalIndex).slideIndex);
            });
        });

        portfolioCarousel.addEventListener('transitionend', (event) => {
            if (
                !(event.target instanceof HTMLElement) ||
                event.propertyName !== 'transform' ||
                event.target !== portfolioSlides[activePortfolioSlideIndex]
            ) {
                return;
            }

            rebasePortfolioCarousel();
        });

        updatePortfolioCarousel();
        window.addEventListener('resize', updatePortfolioCarousel, { passive: true });
    }
}

const chatToggle = document.querySelector('[data-chat-toggle]');
const assistantPanel = document.querySelector('#assistant-panel');
const assistantClose = document.querySelector('[data-chat-close]');
const assistantInput = document.querySelector('#assistant-question');

if (
    chatToggle instanceof HTMLButtonElement &&
    assistantPanel instanceof HTMLElement &&
    assistantInput instanceof HTMLInputElement
) {
    const setChatOpen = (isOpen) => {
        chatToggle.setAttribute('aria-expanded', String(isOpen));
        chatToggle.setAttribute('aria-label', isOpen ? 'Tutup asisten' : 'Buka asisten');
        assistantPanel.classList.toggle('hidden', !isOpen);
    };

    chatToggle.addEventListener('click', () => {
        setChatOpen(chatToggle.getAttribute('aria-expanded') !== 'true');
    });

    assistantClose?.addEventListener('click', () => setChatOpen(false));

    document.querySelectorAll('[data-chat-suggestion]').forEach((suggestion) => {
        suggestion.addEventListener('click', () => {
            if (suggestion instanceof HTMLButtonElement) {
                assistantInput.value = suggestion.textContent?.trim() ?? '';
                assistantInput.focus();
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setChatOpen(false);
        }
    });
}

document.querySelectorAll('[data-content-edit-toggle]').forEach((toggle) => {
    if (!(toggle instanceof HTMLButtonElement)) {
        return;
    }

    toggle.addEventListener('click', () => {
        const panel = toggle.closest('article')?.querySelector('[data-content-edit-panel]');

        if (!(panel instanceof HTMLElement)) {
            return;
        }

        const isOpening = panel.classList.contains('hidden');
        panel.classList.toggle('hidden', !isOpening);
        toggle.setAttribute('aria-expanded', String(isOpening));

        if (isOpening) {
            panel.querySelector('[data-preview-title]')?.focus();
        }
    });
});

document.querySelectorAll('[data-content-preview-form]').forEach((form) => {
    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const dialog = form.querySelector('[data-content-preview-dialog]');
    const stage = dialog?.querySelector('[data-content-preview-stage]');
    const titleInput = form.querySelector('[data-preview-title]');
    const descriptionInput = form.querySelector('[data-preview-description]');
    const metadataInput = form.querySelector('[data-preview-metadata]');
    const imageInput = form.querySelector('[data-preview-image-input]');
    const iconInput = form.querySelector('[data-preview-icon-input]');
    const openButton = form.querySelector('[data-content-preview-open]');

    if (
        !(dialog instanceof HTMLDialogElement) ||
        !(stage instanceof HTMLElement) ||
        !(openButton instanceof HTMLButtonElement)
    ) {
        return;
    }

    let selectedImageSource = '';
    let selectedImageObjectUrl = '';
    const publicImage = stage.querySelector('[data-preview-public-image]');
    const imagePrefix = stage.dataset.imagePrefix ?? '/';
    const contentType = stage.dataset.contentType ?? '';

    const readMetadata = () => {
        if (!(metadataInput instanceof HTMLTextAreaElement) || metadataInput.value.trim() === '') {
            return {};
        }

        try {
            const metadata = JSON.parse(metadataInput.value);

            return metadata !== null && typeof metadata === 'object' && !Array.isArray(metadata)
                ? metadata
                : {};
        } catch {
            return {};
        }
    };

    const metadataValue = (metadata, keys, fallback = '') => {
        for (const key of keys) {
            const value = metadata[key];

            if (typeof value === 'string' || typeof value === 'number') {
                return String(value);
            }
        }

        return fallback;
    };

    const resolveImageSource = (image) => {
        if (image === '' || /^(https?:|blob:|data:|\/)/i.test(image)) {
            return image;
        }

        return `${imagePrefix}${image.replace(/^\/+/, '')}`;
    };

    const serviceIconPaths = {
        map: 'M3 6l6-3 6 3 6-3v15l-6 3-6-3-6 3V6ZM9 3v15M15 6v15',
        calendar: 'M7 2v3M17 2v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z',
        gamepad:
            'M7 16H5.5A3.5 3.5 0 0 1 2 12.5v-1A3.5 3.5 0 0 1 5.5 8h13A3.5 3.5 0 0 1 22 11.5v1a3.5 3.5 0 0 1-3.5 3.5H17l-2 3h-6l-2-3ZM7 11v4M5 13h4M16.5 12h.01M18.5 14h.01',
        bus: 'M5 16V6a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v10M3 16h18M5 16v3M19 16v3M7 19h10M7 7h10v5H7z',
        users: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75',
    };

    const setPublicText = (selector, value, fallback) => {
        stage.querySelectorAll(selector).forEach((element) => {
            if (element instanceof HTMLElement) {
                element.textContent = value || fallback;
            }
        });
    };

    const setPublicVisibility = (element, isVisible) => {
        if (!(element instanceof HTMLElement)) {
            return;
        }

        element.hidden = !isVisible;
        element.classList.toggle('hidden', !isVisible);
    };

    const updatePublicImage = (metadata) => {
        if (!(publicImage instanceof HTMLImageElement)) {
            return;
        }

        const metadataImage = metadataValue(metadata, ['image', 'gambar']);
        const fallbackImage = contentType === 'destinasi' ? `${imagePrefix}images/figma/solo.png` : '';
        const imageSource = selectedImageSource || metadataImage || fallbackImage;
        const resolvedImageSource = resolveImageSource(imageSource);

        if (contentType === 'portofolio') {
            const art = stage.querySelector('[data-preview-public-art]');
            const imageArt = stage.querySelector('[data-preview-public-image-art]');
            const plainMeta = stage.querySelector('[data-preview-public-plain-meta]');
            const imageMeta = stage.querySelector('[data-preview-public-image-meta]');
            const portfolioCard = stage.querySelector('[data-preview-public-portfolio]');
            const category = metadataValue(metadata, ['category', 'kategori'], 'Kategori');
            const year = metadataValue(metadata, ['year', 'tahun'], 'Tahun');
            const location = metadataValue(metadata, ['location', 'lokasi'], 'Lokasi');
            const hasImage = resolvedImageSource !== '';

            setPublicVisibility(art, !hasImage);
            setPublicVisibility(imageArt, hasImage);
            setPublicVisibility(plainMeta, !hasImage);
            setPublicVisibility(imageMeta, hasImage);

            if (portfolioCard instanceof HTMLElement) {
                portfolioCard.classList.toggle('portfolio-card-cover', hasImage);
            }

            setPublicText('[data-preview-public-category]', category, 'Kategori');
            setPublicText('[data-preview-public-image-category]', `${category} / ${year}`, 'Kategori');
            setPublicText('[data-preview-public-location]', location, 'Lokasi');
            setPublicText('[data-preview-public-image-location]', location, 'Lokasi');
            setPublicText('[data-preview-public-year]', year, 'Tahun');
            setPublicVisibility(publicImage, hasImage);

            if (!hasImage) {
                publicImage.removeAttribute('src');
                return;
            }
        }

        if (resolvedImageSource !== '') {
            publicImage.src = resolvedImageSource;
            setPublicVisibility(publicImage, true);
        } else {
            publicImage.removeAttribute('src');
            setPublicVisibility(publicImage, false);
        }
    };

    const updateServiceIcon = (iconName) => {
        const iconContainer = stage.querySelector('[data-preview-public-icon]');
        const pathData = serviceIconPaths[iconName] ?? serviceIconPaths.map;

        if (!(iconContainer instanceof HTMLElement)) {
            return;
        }

        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

        svg.setAttribute('viewBox', '0 0 24 24');
        path.setAttribute('d', pathData);
        svg.append(path);
        iconContainer.replaceChildren(svg);
    };

    const updatePreview = (syncIconFromMetadata = false) => {
        const metadata = readMetadata();
        const title = titleInput instanceof HTMLInputElement ? titleInput.value.trim() : '';
        const description =
            descriptionInput instanceof HTMLTextAreaElement ? descriptionInput.value.trim() : '';
        const iconFromMetadata = metadataValue(metadata, ['icon'], 'map');

        setPublicText('[data-preview-public-title]', title, 'Judul konten');
        setPublicText('[data-preview-public-description]', description, 'Deskripsi konten akan tampil di sini.');
        setPublicText(
            '[data-preview-public-region]',
            metadataValue(metadata, ['region', 'wilayah']),
            'Indonesia',
        );
        setPublicText(
            '[data-preview-public-category]',
            metadataValue(metadata, ['category', 'kategori']),
            'Wisata',
        );
        setPublicText(
            '[data-preview-public-location]',
            metadataValue(metadata, ['location', 'lokasi']),
            'Lokasi',
        );
        setPublicText(
            '[data-preview-public-year]',
            metadataValue(metadata, ['year', 'tahun']),
            'Tahun',
        );
        setPublicText(
            '[data-preview-public-author]',
            metadataValue(metadata, ['author', 'penulis'], title),
            'Nama pemberi testimoni',
        );
        setPublicText(
            '[data-preview-public-role]',
            metadataValue(metadata, ['jabatan']),
            '',
        );
        if (contentType === 'testimoni') {
            const homeQuote = stage.querySelector('.testimonial-card blockquote');

            if (homeQuote instanceof HTMLElement) {
                homeQuote.textContent = `“${description || 'Kutipan testimoni akan tampil di sini.'}”`;
            }
        }

        updatePublicImage(metadata);

        if (contentType === 'layanan') {
            const icon =
                iconInput instanceof HTMLSelectElement ? iconInput.value : iconFromMetadata;
            updateServiceIcon(icon);
        }

        if (contentType === 'klien' || contentType === 'testimoni') {
            const part = metadataValue(metadata, ['bagian'], 'beranda');

            stage.querySelectorAll('[data-preview-variant]').forEach((variant) => {
                if (!(variant instanceof HTMLElement)) {
                    return;
                }

                const isVisible = variant.dataset.previewVariant === part;
                variant.hidden = !isVisible;
                variant.classList.toggle('hidden', !isVisible);
                variant.setAttribute('aria-hidden', String(!isVisible));
            });

            if (contentType === 'klien') {
                const clientLogo = stage.querySelector('.client-logo');

                if (clientLogo instanceof HTMLElement) {
                    clientLogo.setAttribute('aria-label', title || 'Nama klien');
                }
            }
        }

        if (
            syncIconFromMetadata &&
            iconInput instanceof HTMLSelectElement &&
            Array.from(iconInput.options).some((option) => option.value === iconFromMetadata)
        ) {
            iconInput.value = iconFromMetadata;
            updateServiceIcon(iconFromMetadata);
        }
    };

    openButton.addEventListener('click', () => {
        if (!dialog.open) {
            dialog.showModal();
        }
    });

    dialog.querySelector('[data-content-preview-close]')?.addEventListener('click', () => {
        dialog.close();
    });

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });

    titleInput?.addEventListener('input', updatePreview);
    descriptionInput?.addEventListener('input', updatePreview);
    metadataInput?.addEventListener('input', () => updatePreview(true));
    iconInput?.addEventListener('change', updatePreview);

    imageInput?.addEventListener('change', () => {
        if (!(imageInput instanceof HTMLInputElement)) {
            return;
        }

        if (selectedImageObjectUrl !== '') {
            URL.revokeObjectURL(selectedImageObjectUrl);
            selectedImageObjectUrl = '';
        }

        const [file] = imageInput.files ?? [];

        if (file) {
            selectedImageObjectUrl = URL.createObjectURL(file);
            selectedImageSource = selectedImageObjectUrl;
        } else {
            selectedImageSource = '';
        }

        updatePreview(true);
    });

    updatePreview();
});
