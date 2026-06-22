@php
    use App\Support\Mbs;
    $post = $post ?? [];
    $title = $post['title'] ?? 'Blog Post';
    $excerpt = $post['excerpt'] ?? '';
    $image = Mbs::image($post['image'] ?? 'images/blog/qled-tv-guide.jpg');
@endphp

<article class="mbs-blog-card group" @if(!empty($post['slug'])) id="post-{{ $post['slug'] }}" @endif>
    <div class="mbs-blog-card-media">
        <img src="{{ $image }}" alt="{{ $title }}" loading="lazy">
    </div>
    <div class="mbs-blog-card-body">
        <p class="text-xs font-bold uppercase tracking-wide text-primary">{{ $post['date'] ?? '' }} • {{ $post['category'] ?? 'News' }}</p>
        <h3 class="mt-2 line-clamp-2 min-h-[48px] text-lg font-bold text-navy">{{ $title }}</h3>
        <p class="mt-2 flex-1 text-sm leading-relaxed text-muted">{{ $excerpt }}</p>
        <a href="{{ route('blog') }}{{ !empty($post['slug']) ? '#post-'.$post['slug'] : '' }}" class="mbs-section-link">
            Read More <span>→</span>
        </a>
    </div>
</article>
