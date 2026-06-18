<div
    x-show="cartOpen"
    x-cloak
    class="fixed inset-0 z-[70]"
    @keydown.escape.window="cartOpen = false"
>
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="cartOpen = false"></div>

    <aside
        class="absolute right-0 top-0 flex h-full w-full max-w-md flex-col border-l border-border bg-white shadow-2xl"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
    >
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <h2 class="text-lg font-bold text-navy">Your Cart</h2>
            <button type="button" @click="cartOpen = false" class="rounded-lg p-1 text-muted hover:bg-secondary">✕</button>
        </div>

        <div class="flex-1 overflow-y-auto p-5">
            <div class="rounded-xl border border-dashed border-border bg-secondary p-6 text-center">
                <p class="text-sm font-medium text-foreground">Your cart is empty</p>
                <p class="mt-1 text-xs text-muted">Add products from the shop to see them here.</p>
                <a href="{{ route('shop') }}" @click="cartOpen = false" class="mbs-btn mbs-btn-primary mt-4 inline-block">Browse Shop</a>
            </div>
        </div>

        <div class="border-t border-border p-5">
            <div class="flex items-center justify-between text-sm">
                <span class="text-muted">Subtotal</span>
                <span class="font-bold text-navy">Rs 0</span>
            </div>
            <button type="button" class="mbs-btn mbs-btn-primary mt-4 w-full" disabled>Checkout</button>
        </div>
    </aside>
</div>
