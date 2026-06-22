<div
    x-show="quickViewOpen"
    x-cloak
    @open-quick-view.window="quickViewTitle = $event.detail.title; quickViewImage = $event.detail.image || ''; quickViewSlug = $event.detail.slug || ''; quickViewOpen = true"
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
        <img :src="quickViewImage || '{{ asset('placeholder-product.svg') }}'" alt="" class="h-64 w-full bg-white object-contain p-4">
        <div class="p-5">
            <p class="text-sm text-muted">Preview this product and add it to your cart or view full details.</p>
            <div class="mt-6 flex gap-3">
                <template x-if="quickViewSlug">
                    <form :action="`{{ url('/cart/add') }}`" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="slug" :value="quickViewSlug">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="open_cart" value="1">
                        <button type="submit" @click="quickViewOpen = false" class="mbs-btn mbs-btn-primary w-full">Add to Cart</button>
                    </form>
                </template>
                <a :href="quickViewSlug ? `{{ url('/product') }}/${quickViewSlug}` : '{{ route('shop') }}'" class="mbs-btn mbs-btn-outline flex-1 text-center">View Details</a>
            </div>
        </div>
    </div>
</div>
