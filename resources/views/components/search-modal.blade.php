@php
    use App\Support\StorefrontData;
    use App\Support\Mbs;
    $featured = array_slice(StorefrontData::allProducts(), 0, 8);
@endphp

<div
    x-show="searchOpen"
    x-cloak
    class="fixed inset-0 z-[70] flex items-start justify-center bg-slate-900/60 p-4 pt-20 backdrop-blur-sm"
    @keydown.escape.window="searchOpen = false"
>
    <div @click.outside="searchOpen = false" class="w-full max-w-3xl overflow-hidden rounded-2xl border border-border bg-white shadow-2xl">
        <div class="flex items-center gap-3 border-b border-border px-5 py-4">
            <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m20 20-3.5-3.5"/></svg>
            <input type="search" placeholder="Search products, brands, categories..." class="w-full border-0 text-base focus:outline-none focus:ring-0" autofocus>
            <button type="button" @click="searchOpen = false" class="rounded-lg px-2 py-1 text-sm text-muted hover:bg-secondary">Esc</button>
        </div>
        <div class="max-h-[70vh] overflow-y-auto p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-muted">Popular searches</p>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach (['LED TV', 'Soundbar', 'Air Purifier', 'Home Theater', 'Vinyl', 'Sale'] as $term)
                    <button type="button" class="rounded-full border border-border bg-secondary px-3 py-1.5 text-sm font-medium text-foreground hover:border-primary hover:text-primary">{{ $term }}</button>
                @endforeach
            </div>
            <p class="mt-6 text-xs font-bold uppercase tracking-wide text-muted">Featured products</p>
            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                @foreach ($featured as $product)
                    <a href="{{ route('shop') }}" @click="searchOpen = false" class="flex gap-3 rounded-xl border border-border p-3 transition hover:border-primary/30 hover:bg-primary-light/30">
                        <img src="{{ Mbs::image($product['image']) }}" alt="" class="h-16 w-16 rounded-lg object-cover bg-secondary">
                        <div class="min-w-0">
                            <p class="line-clamp-2 text-sm font-semibold text-navy">{{ $product['name'] }}</p>
                            <p class="mt-1 text-sm font-bold text-primary">{{ Mbs::price($product['price']) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
