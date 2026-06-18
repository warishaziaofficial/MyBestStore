<div
    x-show="quickViewOpen"
    x-cloak
    @open-quick-view.window="quickViewTitle = $event.detail.title; quickViewImage = $event.detail.image || ''; quickViewOpen = true"
    class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
    @keydown.escape.window="quickViewOpen = false"
>
    <div @click.outside="quickViewOpen = false" class="w-full max-w-lg overflow-hidden rounded-2xl border border-border bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-border p-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-primary">Quick View</p>
                <h2 class="mt-1 text-xl font-bold text-navy" x-text="quickViewTitle"></h2>
            </div>
            <button type="button" @click="quickViewOpen = false" class="mbs-icon-btn">✕</button>
        </div>
        <img :src="quickViewImage || '{{ asset('placeholder-product.svg') }}'" alt="" class="h-64 w-full bg-secondary object-cover">
        <div class="p-5">
            <p class="text-sm text-muted">Product details will load from the database when the CMS/backend is connected.</p>
            <div class="mt-6 flex gap-3">
                <button type="button" @click="cartOpen = true; quickViewOpen = false" class="mbs-btn mbs-btn-primary flex-1">Add to Cart</button>
                <a href="{{ route('shop') }}" class="mbs-btn-outline flex-1 text-center">View Details</a>
            </div>
        </div>
    </div>
</div>
