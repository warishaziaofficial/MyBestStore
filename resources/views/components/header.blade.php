@php
    use App\Support\Mbs;
    $navigation = $navigation ?? config('storefront.navigation', []);
    $megaMenu = $megaMenu ?? \App\Support\StorefrontData::megaMenu();
@endphp

<header class="site-header mbs-header" @mouseleave="openDropdown = null">
    <div class="mbs-topbar">
        <div class="mbs-container mbs-topbar-inner">
            <p>Free delivery on orders above Rs 10,000 across Pakistan</p>
            <p class="hidden sm:block">Call: +92 300 1234567</p>
        </div>
    </div>

    <div class="mbs-header-separator" aria-hidden="true"></div>

    <div class="mbs-header-main">
        <div class="mbs-container mbs-header-inner">
            <div class="mbs-header-brand">
                <button
                    type="button"
                    class="mbs-icon-btn mbs-icon-btn--menu lg:hidden"
                    @click="mobileNavOpen = !mobileNavOpen"
                    aria-label="Toggle menu"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ route('home') }}" class="mbs-logo-link">
                    <img src="{{ asset('logo.png') }}" alt="DigitalWares.pk" class="mbs-logo-img">
                </a>
            </div>

            <nav class="mbs-nav">
                @foreach ($navigation as $item)
                    @if (!empty($item['mega']))
                        <a
                            href="{{ Mbs::navUrl($item) }}"
                            class="mbs-nav-link {{ request()->routeIs('shop') ? 'is-active' : '' }}"
                            @mouseenter="openDropdown = '{{ $item['label'] }}'"
                        >
                            {{ $item['label'] }}
                            <span class="text-xs">▾</span>
                        </a>
                    @else
                        <a
                            href="{{ Mbs::navUrl($item) }}"
                            class="mbs-nav-link {{ request()->routeIs($item['href']) ? 'is-active' : '' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="mbs-header-actions">
                <button type="button" @click="searchOpen = true" class="mbs-icon-btn" aria-label="Search">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m20 20-3.5-3.5"/></svg>
                </button>
                <button
                    type="button"
                    @click="openAuth('signin')"
                    class="mbs-icon-btn hidden sm:inline-flex"
                    :aria-label="customer ? `Account: ${customer.name}` : 'Sign in'"
                    :title="customer ? customer.name : 'Sign in'"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M4 20c1.5-4 6.5-4 8-4s6.5 0 8 4"/></svg>
                </button>
                <button type="button" class="mbs-icon-btn relative" aria-label="Wishlist" @click="window.location.href='{{ route('wishlist') }}'">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 5.5-7 10-7 10Z"/></svg>
                    <span class="mbs-cart-badge" x-show="wishlistSlugs.length > 0" x-text="wishlistSlugs.length" x-cloak></span>
                </button>
                <button type="button" @click="cartOpen = true" class="mbs-icon-btn relative" aria-label="Cart">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" stroke-width="2" d="M6 7l-2-3H1"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                    <span class="mbs-cart-badge" x-text="cartCount"></span>
                </button>
            </div>
        </div>

        <div
            x-show="openDropdown === 'Shop'"
            x-cloak
            @mouseenter="openDropdown = 'Shop'"
            class="shop-mega-menu mbs-mega-menu"
        >
            <div class="mbs-container mbs-mega-menu-inner">
                @foreach ($megaMenu as $column)
                    <div>
                        <h3 class="mbs-mega-title">{{ $column['title'] }}</h3>
                        <ul class="mbs-mega-links">
                            @foreach ($column['links'] as $link)
                                <li>
                                    <a href="{{ Mbs::navUrl($link) }}" class="mbs-mega-menu-item">
                                        <span class="mbs-mega-menu-thumb">
                                            <img
                                                src="{{ Mbs::image($link['image'] ?? 'placeholder-product.svg') }}"
                                                alt=""
                                                class="mbs-mega-menu-thumb-img"
                                                loading="lazy"
                                            >
                                        </span>
                                        <span class="mbs-mega-menu-label">{{ $link['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div x-show="mobileNavOpen" x-cloak class="border-t border-border bg-white px-4 py-4 lg:hidden">
        <nav class="space-y-1">
            @foreach ($navigation as $item)
                @if (!empty($item['mega']))
                    <div x-data="{ open: false }">
                        <div class="flex w-full items-center gap-1">
                            <a
                                href="{{ Mbs::navUrl($item) }}"
                                @click="mobileNavOpen = false"
                                class="flex-1 rounded-lg px-3 py-2.5 text-sm font-semibold text-foreground hover:bg-primary-light"
                            >
                                {{ $item['label'] }}
                            </a>
                            <button
                                type="button"
                                @click="open = !open"
                                class="rounded-lg px-2.5 py-2.5 text-sm font-semibold text-muted"
                                aria-label="Toggle {{ $item['label'] }} categories"
                            >
                                <span x-text="open ? '▴' : '▾'"></span>
                            </button>
                        </div>
                        <div x-show="open" x-cloak class="ml-3 space-y-3 border-l border-border py-2 pl-4">
                            @foreach ($megaMenu as $column)
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-navy">{{ $column['title'] }}</p>
                                    <div class="mt-2 space-y-2">
                                        @foreach ($column['links'] as $link)
                                            <a href="{{ Mbs::navUrl($link) }}" @click="mobileNavOpen = false" class="block text-sm text-muted">{{ $link['label'] }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ Mbs::navUrl($item) }}" @click="mobileNavOpen = false" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-foreground hover:bg-primary-light">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
            <button type="button" @click="openAuth('signin'); mobileNavOpen = false" class="block w-full rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-primary">
                Sign In
            </button>
        </nav>
    </div>
</header>
