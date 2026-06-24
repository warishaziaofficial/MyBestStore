@php
    $dashPrimary = [
        ['label' => 'Products', 'route' => route('cms.products.index'), 'icon' => '📦'],
        ['label' => 'Orders', 'route' => route('cms.orders.index'), 'icon' => '🛒'],
        ['label' => 'Reports', 'route' => route('cms.reports'), 'icon' => '📊'],
    ];

    $dashMenus = [
        'Catalog' => [
            ['label' => 'Featured Products', 'route' => route('cms.merchandising.featured')],
            ['label' => 'New Arrivals', 'route' => route('cms.merchandising.new-arrivals')],
            ['label' => 'Categories', 'route' => route('cms.resource.index', 'categories')],
            ['label' => 'Brands', 'route' => route('cms.resource.index', 'brands')],
        ],
        'Storefront' => [
            ['label' => 'Hero Slides', 'route' => route('cms.resource.index', 'hero-slides')],
            ['label' => 'Blog', 'route' => route('cms.resource.index', 'blog-posts')],
            ['label' => 'Media Library', 'route' => route('cms.resource.index', 'media')],
            ['label' => 'Footer Settings', 'route' => route('cms.settings.footer')],
        ],
        'Engagement' => [
            ['label' => 'Reviews', 'route' => route('cms.resource.index', 'reviews')],
            ['label' => 'Inquiries', 'route' => route('cms.resource.index', 'inquiries')],
            ['label' => 'Newsletter', 'route' => route('cms.resource.index', 'newsletter-subscribers')],
        ],
        'Integrations' => [
            ['label' => 'Chatbot', 'route' => route('cms.settings.chatbot')],
            ['label' => 'Social Integration', 'route' => route('cms.social.index')],
            ['label' => 'Dispatch Queue', 'route' => route('cms.dispatch.queue')],
        ],
    ];
@endphp

<nav class="sf-dash-nav" aria-label="Dashboard shortcuts">
    @foreach ($dashPrimary as $link)
        <a href="{{ $link['route'] }}" class="sf-dash-nav__primary">
            <span class="sf-dash-nav__icon" aria-hidden="true">{{ $link['icon'] }}</span>
            {{ $link['label'] }}
        </a>
    @endforeach

    <span class="sf-dash-nav__divider" aria-hidden="true"></span>

    @foreach ($dashMenus as $menuLabel => $menuLinks)
        <details class="sf-dash-nav__menu" data-dash-menu>
            <summary class="sf-dash-nav__menu-btn">{{ $menuLabel }} <span aria-hidden="true">▾</span></summary>
            <div class="sf-dash-nav__dropdown">
                @foreach ($menuLinks as $menuLink)
                    <a href="{{ $menuLink['route'] }}" class="sf-dash-nav__dropdown-link">{{ $menuLink['label'] }}</a>
                @endforeach
            </div>
        </details>
    @endforeach

    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="sf-dash-nav__storefront">View storefront ↗</a>
</nav>

<script>
(function () {
    document.querySelectorAll('[data-dash-menu]').forEach(function (menu) {
        menu.addEventListener('toggle', function () {
            if (!menu.open) {
                return;
            }
            document.querySelectorAll('[data-dash-menu]').forEach(function (other) {
                if (other !== menu) {
                    other.open = false;
                }
            });
        });
    });

    document.addEventListener('click', function (event) {
        if (event.target.closest('[data-dash-menu]')) {
            return;
        }
        document.querySelectorAll('[data-dash-menu]').forEach(function (menu) {
            menu.open = false;
        });
    });
})();
</script>
