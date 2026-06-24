/**
 * Global toast helper for nested Alpine components (e.g. product review form).
 * Uses storefront toast when available; otherwise shows a safe DOM fallback.
 */
window.showToast = function (message, type = 'success') {
    if (!message || typeof message !== 'string') {
        return;
    }

    try {
        if (window.Alpine && document.body) {
            const data = window.Alpine.$data(document.body);

            if (data && typeof data.showToast === 'function') {
                data.showToast(message, type);
                return;
            }
        }
    } catch (error) {
        // ignore Alpine lookup errors
    }

    let el = document.getElementById('mbs-toast-fallback');

    if (!el) {
        el = document.createElement('div');
        el.id = 'mbs-toast-fallback';
        el.className = 'mbs-toast';
        el.setAttribute('role', 'status');
        el.setAttribute('aria-live', 'polite');
        document.body.appendChild(el);
    }

    el.textContent = message;
    el.hidden = false;

    clearTimeout(window.__mbsToastFallbackTimer);
    window.__mbsToastFallbackTimer = setTimeout(() => {
        el.hidden = true;
    }, 3200);
};

document.addEventListener('alpine:init', () => {
    Alpine.data('mbsStorefront', () => ({
        searchOpen: false,
        loginOpen: false,
        cartOpen: false,
        quickViewOpen: false,
        quickViewTitle: '',
        quickViewImage: '',
        quickViewSlug: '',
        filterOpen: false,
        mobileNavOpen: false,
        openDropdown: null,
        cartCount: 0,
        wishlistSlugs: [],
        compareSlugs: [],
        searchQuery: '',
        searchLoading: false,
        searchHtml: '',
        searchEmpty: false,
        toastMessage: '',
        toastVisible: false,
        authView: 'signin',
        customer: null,
        authUrls: {},
        authError: '',
        authSuccess: '',
        authSubmitting: false,
        showLoginPassword: false,
        showRegisterPassword: false,
        showRegisterPasswordConfirm: false,
        newsletterPopupOpen: false,
        newsletterEmail: '',
        newsletterError: '',
        newsletterSuccess: false,
        newsletterSubmitting: false,

        init() {
            const boot = window.__MBS__ || {};
            this.cartCount = boot.cartCount || 0;
            this.cartOpen = Boolean(boot.openCart);
            this.wishlistSlugs = Array.isArray(boot.wishlistSlugs) ? [...boot.wishlistSlugs] : [];
            this.compareSlugs = Array.isArray(boot.compareSlugs) ? [...boot.compareSlugs] : [];
            this.customer = boot.customer || null;
            this.authUrls = boot.authUrls || {};

            const stored = localStorage.getItem('mbs_wishlist');
            if (stored) {
                try {
                    const parsed = JSON.parse(stored);
                    if (Array.isArray(parsed) && parsed.length) {
                        this.wishlistSlugs = [...new Set([...this.wishlistSlugs, ...parsed])];
                    }
                } catch (error) {
                    // ignore invalid localStorage
                }
            }

            this.persistWishlist();
            this.syncWishlistToServer();

            const storedCompare = localStorage.getItem('mbs_compare');
            if (storedCompare) {
                try {
                    const parsedCompare = JSON.parse(storedCompare);
                    if (Array.isArray(parsedCompare) && parsedCompare.length) {
                        this.compareSlugs = [...new Set([...this.compareSlugs, ...parsedCompare])];
                    }
                } catch (error) {
                    // ignore invalid localStorage
                }
            }

            this.persistCompare();
            this.syncCompareToServer();

            this.$watch('searchOpen', (open) => {
                if (!open) {
                    this.searchQuery = '';
                    this.searchHtml = '';
                    this.searchEmpty = false;
                    this.searchLoading = false;
                }
            });

            this.$watch('loginOpen', (open) => {
                if (!open) {
                    this.resetAuthState();
                }
            });

            this.initNewsletterPopup();
        },

        csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        },

        wishlistCount() {
            return this.wishlistSlugs.length;
        },

        isWishlisted(slug) {
            return this.wishlistSlugs.includes(slug);
        },

        compareCount() {
            return this.compareSlugs.length;
        },

        isCompared(slug) {
            return this.compareSlugs.includes(slug);
        },

        persistWishlist() {
            localStorage.setItem('mbs_wishlist', JSON.stringify(this.wishlistSlugs));
        },

        persistCompare() {
            localStorage.setItem('mbs_compare', JSON.stringify(this.compareSlugs));
        },

        async syncCompareToServer() {
            if (!this.compareSlugs.length) {
                return;
            }

            try {
                const response = await fetch('/compare/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({ slugs: this.compareSlugs }),
                });

                const data = await response.json();

                if (response.ok && Array.isArray(data.slugs)) {
                    const boot = window.__MBS__ || {};
                    const hadSessionItems = (boot.compareSlugs || []).length;
                    this.compareSlugs = data.slugs;
                    this.persistCompare();

                    if (!hadSessionItems && data.slugs.length && window.location.pathname.endsWith('/compare')) {
                        window.location.reload();
                    }
                }
            } catch (error) {
                // session sync is best-effort for guests
            }
        },

        async syncWishlistToServer() {
            if (!this.wishlistSlugs.length) {
                return;
            }

            try {
                const response = await fetch('/wishlist/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({ slugs: this.wishlistSlugs }),
                });

                const data = await response.json();

                if (response.ok && Array.isArray(data.slugs)) {
                    const boot = window.__MBS__ || {};
                    const hadSessionItems = (boot.wishlistSlugs || []).length;
                    this.wishlistSlugs = data.slugs;
                    this.persistWishlist();

                    if (!hadSessionItems && data.slugs.length && window.location.pathname.endsWith('/wishlist')) {
                        window.location.reload();
                    }
                }
            } catch (error) {
                // session sync is best-effort for guests
            }
        },

        async toggleWishlist(slug) {
            if (!slug) {
                return;
            }

            try {
                const response = await fetch('/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({ slug }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Wishlist update failed.');
                }

                this.wishlistSlugs = Array.isArray(data.slugs) ? data.slugs : [];
                this.persistWishlist();
            } catch (error) {
                this.showToast('Could not update wishlist.');
            }
        },

        async toggleCompare(slug) {
            if (!slug) {
                return;
            }

            try {
                const response = await fetch('/compare/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({ slug }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Compare update failed.');
                }

                this.compareSlugs = Array.isArray(data.slugs) ? data.slugs : [];
                this.persistCompare();
                this.showToast(data.message || 'Compare list updated.');
            } catch (error) {
                this.showToast('Could not update compare list.');
            }
        },

        async refreshCartDrawer() {
            try {
                const response = await fetch('/cart/drawer', {
                    headers: { Accept: 'text/html' },
                });

                if (!response.ok) {
                    return;
                }

                const panel = document.getElementById('cart-drawer-panel');

                if (!panel) {
                    return;
                }

                panel.innerHTML = await response.text();

                if (typeof Alpine.initTree === 'function') {
                    Alpine.initTree(panel);
                }
            } catch (error) {
                // drawer refresh is best-effort
            }
        },

        async addToCart(slug, event, openDrawer = true) {
            if (event) {
                event.preventDefault();
            }

            if (!slug) {
                return;
            }

            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({ slug, quantity: 1 }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Add to cart failed.');
                }

                this.cartCount = data.count ?? this.cartCount;

                if (openDrawer) {
                    await this.refreshCartDrawer();
                    this.cartOpen = true;
                } else {
                    this.showToast(data.message || 'Product added to cart.');
                }
            } catch (error) {
                this.showToast('Product could not be added to cart.');
            }
        },

        showToast(message) {
            this.toastMessage = message;
            this.toastVisible = true;

            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => {
                this.toastVisible = false;
            }, 3200);
        },

        openAuth(view = 'signin') {
            this.authView = view;
            this.clearAuthMessages();
            this.loginOpen = true;
        },

        closeAuth() {
            this.loginOpen = false;
        },

        continueAsGuest() {
            this.closeAuth();
        },

        clearAuthMessages() {
            this.authError = '';
            this.authSuccess = '';
        },

        resetAuthState() {
            this.authView = 'signin';
            this.clearAuthMessages();
            this.authSubmitting = false;
            this.showLoginPassword = false;
            this.showRegisterPassword = false;
            this.showRegisterPasswordConfirm = false;
        },

        validateEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email || '').trim());
        },

        async parseAuthResponse(response) {
            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.success) {
                let message = data.message;

                if (!message && data.errors) {
                    message = Object.values(data.errors).flat().join(' ');
                }

                throw new Error(message || 'Something went wrong. Please try again.');
            }

            return data;
        },

        async submitLogin(event) {
            event.preventDefault();
            this.clearAuthMessages();

            const form = event.target;
            const email = form.email.value.trim();
            const password = form.password.value;

            if (!email) {
                this.authError = 'Email is required.';
                return;
            }

            if (!this.validateEmail(email)) {
                this.authError = 'Enter a valid email address.';
                return;
            }

            if (!password) {
                this.authError = 'Password is required.';
                return;
            }

            this.authSubmitting = true;

            try {
                const data = await this.parseAuthResponse(await fetch(this.authUrls.login, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({
                        email,
                        password,
                        remember: Boolean(form.remember?.checked),
                    }),
                }));

                this.customer = data.customer;
                this.authSuccess = data.message;
                form.reset();
                window.showToast(data.message, 'success');
                if (window.MbsChatwoot) {
                    window.MbsChatwoot.identifyUser(data.customer);
                }
                setTimeout(() => this.closeAuth(), 500);
            } catch (error) {
                this.authError = error.message || 'Sign in failed. Please try again.';
            } finally {
                this.authSubmitting = false;
            }
        },

        async submitRegister(event) {
            event.preventDefault();
            this.clearAuthMessages();

            const form = event.target;
            const name = form.name.value.trim();
            const email = form.email.value.trim();
            const phone = form.phone.value.trim();
            const password = form.password.value;
            const passwordConfirmation = form.password_confirmation.value;

            if (!name) {
                this.authError = 'Full name is required.';
                return;
            }

            if (!email) {
                this.authError = 'Email is required.';
                return;
            }

            if (!this.validateEmail(email)) {
                this.authError = 'Enter a valid email address.';
                return;
            }

            if (!phone) {
                this.authError = 'Phone number is required.';
                return;
            }

            if (!password) {
                this.authError = 'Password is required.';
                return;
            }

            if (password.length < 8) {
                this.authError = 'Password must be at least 8 characters.';
                return;
            }

            if (password !== passwordConfirmation) {
                this.authError = 'Passwords do not match.';
                return;
            }

            this.authSubmitting = true;

            try {
                const data = await this.parseAuthResponse(await fetch(this.authUrls.register, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        phone,
                        password,
                        password_confirmation: passwordConfirmation,
                    }),
                }));

                this.customer = data.customer;
                this.authSuccess = data.message;
                form.reset();
                window.showToast(data.message, 'success');
                if (window.MbsChatwoot) {
                    window.MbsChatwoot.identifyUser(data.customer);
                }
                setTimeout(() => this.closeAuth(), 500);
            } catch (error) {
                this.authError = error.message || 'Account could not be created.';
            } finally {
                this.authSubmitting = false;
            }
        },

        async submitForgotPassword(event) {
            event.preventDefault();
            this.clearAuthMessages();

            const form = event.target;
            const email = form.email.value.trim();

            if (!email) {
                this.authError = 'Email is required.';
                return;
            }

            if (!this.validateEmail(email)) {
                this.authError = 'Enter a valid email address.';
                return;
            }

            this.authSubmitting = true;

            try {
                const data = await this.parseAuthResponse(await fetch(this.authUrls.forgotPassword, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({ email }),
                }));

                this.authSuccess = data.message;
                form.reset();
            } catch (error) {
                this.authError = error.message || 'Reset request could not be sent.';
            } finally {
                this.authSubmitting = false;
            }
        },

        async logoutCustomer() {
            this.clearAuthMessages();

            try {
                const data = await this.parseAuthResponse(await fetch(this.authUrls.logout, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                }));

                this.customer = null;
                this.authSuccess = data.message;
                window.showToast(data.message, 'success');
                if (window.MbsChatwoot) {
                    window.MbsChatwoot.resetUser();
                }
            } catch (error) {
                this.authError = error.message || 'Could not sign out.';
            }
        },

        async submitTrackOrder(event) {
            event.preventDefault();
            this.clearAuthMessages();

            const form = event.target;
            const orderNumber = form.order_number.value.trim();
            const phone = form.phone?.value?.trim() || '';
            const email = form.email?.value?.trim() || '';

            if (!orderNumber) {
                this.authError = 'Order number is required.';
                return;
            }

            if (!phone && !email) {
                this.authError = 'Phone number or email is required.';
                return;
            }

            if (email && !this.validateEmail(email)) {
                this.authError = 'Enter a valid email address.';
                return;
            }

            this.authSubmitting = true;

            try {
                const data = await this.parseAuthResponse(await fetch(this.authUrls.trackOrder, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({
                        order_number: orderNumber,
                        phone: phone || null,
                        email: email || null,
                    }),
                }));

                this.authSuccess = data.message;

                if (data.redirect_url) {
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 700);
                }
            } catch (error) {
                this.authError = error.message || 'Order could not be found.';
            } finally {
                this.authSubmitting = false;
            }
        },

        setPopularSearch(term) {
            this.searchQuery = term;
            this.runSearch();
        },

        shouldShowNewsletterPopup() {
            if (localStorage.getItem('mbs_newsletter_subscribed') === '1') {
                return false;
            }

            const dismissed = localStorage.getItem('mbs_newsletter_popup_dismissed');

            if (! dismissed) {
                return true;
            }

            const dismissedAt = Number.parseInt(dismissed, 10);
            const oneDayMs = 24 * 60 * 60 * 1000;

            return ! Number.isFinite(dismissedAt) || Date.now() - dismissedAt > oneDayMs;
        },

        initNewsletterPopup() {
            if (! this.shouldShowNewsletterPopup()) {
                return;
            }

            const delayMs = 1000 + Math.floor(Math.random() * 1001);

            setTimeout(() => {
                if (this.shouldShowNewsletterPopup()) {
                    this.newsletterPopupOpen = true;
                }
            }, delayMs);
        },

        closeNewsletterPopup() {
            this.newsletterPopupOpen = false;
            localStorage.setItem('mbs_newsletter_popup_dismissed', String(Date.now()));
        },

        validateEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email || '').trim());
        },

        async submitNewsletter(event) {
            event.preventDefault();
            this.newsletterError = '';

            const email = this.newsletterEmail.trim();

            if (! email) {
                this.newsletterError = 'Email is required.';
                return;
            }

            if (! this.validateEmail(email)) {
                this.newsletterError = 'Enter a valid email address.';
                return;
            }

            this.newsletterSubmitting = true;

            try {
                this.newsletterSuccess = true;
                localStorage.setItem('mbs_newsletter_subscribed', '1');
                localStorage.setItem('mbs_newsletter_subscribed_email', email);
                localStorage.setItem('mbs_newsletter_popup_dismissed', String(Date.now()));

                setTimeout(() => {
                    this.closeNewsletterPopup();
                }, 2200);
            } finally {
                this.newsletterSubmitting = false;
            }
        },

        initSearchResults() {
            this.$nextTick(() => {
                const el = this.$refs.searchResults;

                if (!el) {
                    return;
                }

                if (typeof Alpine.destroyTree === 'function') {
                    Alpine.destroyTree(el);
                }

                Alpine.initTree(el);
            });
        },

        async runSearch() {
            const query = this.searchQuery.trim();

            if (!query) {
                this.searchHtml = '';
                this.searchEmpty = false;
                this.searchLoading = false;
                return;
            }

            this.searchLoading = true;

            try {
                const response = await fetch(`/search/products?q=${encodeURIComponent(query)}`, {
                    headers: { Accept: 'application/json' },
                });
                const data = await response.json();
                this.searchHtml = data.html || '';
                this.searchEmpty = (data.count || 0) === 0;
                this.initSearchResults();
            } catch (error) {
                this.searchHtml = '';
                this.searchEmpty = true;
            } finally {
                this.searchLoading = false;
            }
        },
    }));

    Alpine.data('mbsShopCatalog', (config = {}) => ({
        shopView: 'grid',
        filterOpen: false,

        init() {
            const params = new URLSearchParams(window.location.search);
            const urlView = params.get('view');
            const storedView = localStorage.getItem('mbs_shop_view');
            const initialView = config.view || 'grid';

            this.shopView = ['grid', 'list'].includes(urlView)
                ? urlView
                : (['grid', 'list'].includes(storedView) ? storedView : initialView);

            if (!urlView && this.shopView) {
                this.syncViewToUrl(this.shopView, true);
            }
        },

        setView(mode) {
            if (!['grid', 'list'].includes(mode)) {
                return;
            }

            this.shopView = mode;
            localStorage.setItem('mbs_shop_view', mode);
            this.syncViewToUrl(mode, false);

            const form = this.$el.querySelector('form.shop-catalog-form');
            const hiddenView = form?.querySelector('input[name="view"]');

            if (hiddenView) {
                hiddenView.value = mode;
            }
        },

        syncViewToUrl(mode, replaceOnly) {
            const url = new URL(window.location.href);

            if (mode === 'grid') {
                url.searchParams.delete('view');
            } else {
                url.searchParams.set('view', mode);
            }

            const next = url.pathname + url.search + url.hash;
            const current = window.location.pathname + window.location.search + window.location.hash;

            if (next === current) {
                return;
            }

            if (replaceOnly) {
                window.history.replaceState({}, '', next);
            } else {
                window.history.pushState({}, '', next);
            }
        },

        onFilterSubmit() {
            this.filterOpen = false;
        },
    }));
});

function initHeroMainCarousel() {
    const carousel = document.querySelector('[data-hero-carousel]');
    if (!carousel) {
        return;
    }

    const slides = Array.from(carousel.querySelectorAll('[data-hero-slide]'));
    const dots = Array.from(carousel.querySelectorAll('.hero-dot'));
    const prevBtn = carousel.querySelector('.hero-prev');
    const nextBtn = carousel.querySelector('.hero-next');

    if (!slides.length) {
        return;
    }

    let current = slides.findIndex((slide) => slide.classList.contains('active'));
    if (current < 0) {
        current = 0;
    }

    let timer = null;
    let paused = false;
    const intervalMs = 4500;

    function goTo(index) {
        current = (index + slides.length) % slides.length;

        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle('active', slideIndex === current);
        });

        dots.forEach((dot, dotIndex) => {
            dot.classList.toggle('active', dotIndex === current);
        });
    }

    function nextSlide() {
        goTo(current + 1);
    }

    function prevSlide() {
        goTo(current - 1);
    }

    function startAutoplay() {
        clearInterval(timer);
        if (paused || slides.length < 2) {
            return;
        }
        timer = setInterval(nextSlide, intervalMs);
    }

    function restartAutoplay() {
        clearInterval(timer);
        startAutoplay();
    }

    prevBtn?.addEventListener('click', () => {
        prevSlide();
        restartAutoplay();
    });

    nextBtn?.addEventListener('click', () => {
        nextSlide();
        restartAutoplay();
    });

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            goTo(index);
            restartAutoplay();
        });
    });

    carousel.addEventListener('mouseenter', () => {
        paused = true;
        clearInterval(timer);
    });

    carousel.addEventListener('mouseleave', () => {
        paused = false;
        startAutoplay();
    });

    carousel.addEventListener('focusin', () => {
        paused = true;
        clearInterval(timer);
    });

    carousel.addEventListener('focusout', (event) => {
        if (carousel.contains(event.relatedTarget)) {
            return;
        }
        paused = false;
        startAutoplay();
    });

    goTo(current);
    startAutoplay();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroMainCarousel);
} else {
    initHeroMainCarousel();
}

function initFeatureCarousel() {
    const carousel = document.querySelector('[data-feature-carousel]');
    if (!carousel) {
        return;
    }

    const viewport = carousel.querySelector('.mbs-feature-carousel-viewport');
    const track = carousel.querySelector('.mbs-feature-carousel-track');
    const items = Array.from(carousel.querySelectorAll('.mbs-feature-carousel-item'));
    const prevBtn = carousel.querySelector('.mbs-feature-carousel-nav--prev');
    const nextBtn = carousel.querySelector('.mbs-feature-carousel-nav--next');

    if (!viewport || !track || !items.length) {
        return;
    }

    let index = 0;

    function itemsPerView() {
        const width = viewport.clientWidth;
        if (width >= 1280) return 6;
        if (width >= 1024) return 5;
        if (width >= 640) return 4;
        return 3;
    }

    function maxIndex() {
        return Math.max(0, items.length - itemsPerView());
    }

    function gapSize() {
        const styles = window.getComputedStyle(track);
        return parseFloat(styles.columnGap || styles.gap || '0') || 0;
    }

    function update() {
        const perView = itemsPerView();
        const gap = gapSize();
        const itemWidth = (viewport.clientWidth - gap * (perView - 1)) / perView;

        items.forEach((item) => {
            item.style.flex = `0 0 ${itemWidth}px`;
            item.style.width = `${itemWidth}px`;
        });

        if (index > maxIndex()) {
            index = maxIndex();
        }

        const offset = index * (itemWidth + gap);
        track.style.transform = `translateX(-${offset}px)`;

        if (prevBtn) {
            prevBtn.disabled = index <= 0;
        }
        if (nextBtn) {
            nextBtn.disabled = index >= maxIndex();
        }
    }

    prevBtn?.addEventListener('click', () => {
        if (index > 0) {
            index -= 1;
            update();
        }
    });

    nextBtn?.addEventListener('click', () => {
        if (index < maxIndex()) {
            index += 1;
            update();
        }
    });

    window.addEventListener('resize', update);
    update();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFeatureCarousel);
} else {
    initFeatureCarousel();
}
