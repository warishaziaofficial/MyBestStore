@php
    use App\Support\Mbs;
    $post = $post ?? [];
    $title = $post['title'] ?? 'Blog Post';
    $excerpt = $post['excerpt'] ?? '';
    $image = Mbs::image($post['image'] ?? 'placeholder-product.svg');

    if (! empty($post['product_slug'])) {
        $href = route('product.show', $post['product_slug']);
        $linkLabel = 'View Product';
    } elseif (! empty($post['category_slug'])) {
        $href = route('shop', ['category' => $post['category_slug']]);
        $linkLabel = 'Shop Category';
    } else {
        $href = route('blog').(! empty($post['slug']) ? '#post-'.$post['slug'] : '');
        $linkLabel = 'Read More';
    }
@endphp

<article class="mbs-blog-card group" @if(!empty($post['slug'])) id="post-{{ $post['slug'] }}" @endif>
    <a href="{{ $href }}" class="mbs-blog-card-media">
        <img src="{{ $image }}" alt="{{ $title }}" loading="lazy">
    </a>
    <div class="mbs-blog-card-body">
        <p class="text-xs font-bold uppercase tracking-wide text-primary">{{ $post['date'] ?? '' }} • {{ $post['category'] ?? 'Products' }}</p>
        <h3 class="mt-2 line-clamp-2 min-h-[48px] text-lg font-bold text-navy">
            <a href="{{ $href }}" class="hover:text-primary">{{ $title }}</a>
        </h3>
        <p class="mt-2 flex-1 text-sm leading-relaxed text-muted">{{ $excerpt }}</p>
        <a href="{{ $href }}" class="mbs-section-link">
            {{ $linkLabel }} <span>→</span>
        </a>
    </div>
</article>
