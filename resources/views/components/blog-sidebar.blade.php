@php
    use App\Support\Mbs;
    $blogCategories = $blogCategories ?? [];
    $blogTags = $blogTags ?? [];
    $posts = $posts ?? [];
@endphp

<aside class="shop-sidebar-card lg:sticky lg:top-28 lg:h-fit">
    <div class="p-5">
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wide text-navy">Categories</h3>
            <ul class="mt-3 space-y-2.5 text-sm text-muted">
                @foreach ($blogCategories as $category)
                    <li><a href="{{ route('blog') }}" class="font-medium hover:text-primary">{{ $category['label'] }}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="mt-6">
            <h3 class="text-sm font-bold uppercase tracking-wide text-navy">Tags</h3>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($blogTags as $tag)
                    <span class="rounded-full border border-border bg-secondary px-3 py-1 text-xs font-medium text-muted">{{ $tag }}</span>
                @endforeach
            </div>
        </div>
        <div class="mt-6">
            <h3 class="text-sm font-bold uppercase tracking-wide text-navy">Recent Posts</h3>
            <ul class="mt-3 space-y-3 text-sm">
                @foreach (array_slice($posts, 0, 4) as $post)
                    <li>
                        <a href="{{ route('blog') }}" class="font-semibold text-navy hover:text-primary">{{ $post['title'] }}</a>
                        <p class="text-xs text-muted">{{ $post['date'] ?? '' }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</aside>
