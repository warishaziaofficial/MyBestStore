@props(['products' => []])

<div class="search-modal-product-grid mbs-product-grid">
    @foreach ($products as $product)
        @include('components.product-card', ['product' => $product])
    @endforeach
</div>
