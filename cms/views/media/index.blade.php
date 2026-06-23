@extends('cms::layouts.admin')

@section('title', $config['title'])

@section('page_heading')
<div class="sf-page-head">
    <div>
        <h1 class="sf-page-title">{{ $config['title'] }}</h1>
        <p class="sf-page-subtitle">Upload and manage images used across the storefront.</p>
    </div>
    @if ($canEdit)
        <a href="{{ route('cms.resource.create', $entity) }}" class="sf-btn sf-btn-primary">Upload Image</a>
    @endif
</div>
@endsection

@section('content')
@if ($canEdit)
    <div class="cms-panel cms-panel--compact">
        <p class="cms-muted">After upload, copy the <strong>path</strong> shown below into product, banner, or hero image fields.</p>
    </div>
@endif

<div class="cms-media-grid">
    @forelse ($items as $item)
        <div class="cms-media-card">
            <div class="cms-media-card-image">
                <img src="{{ asset($item->path) }}" alt="{{ $item->alt_text ?? $item->filename }}">
            </div>
            <div class="cms-media-card-body">
                <strong class="cms-media-filename">{{ $item->filename }}</strong>
                <code class="cms-media-path" data-copy-path>{{ $item->path }}</code>
                <button type="button" class="cms-copy-btn" data-copy="{{ $item->path }}">Copy path</button>
                @if ($item->alt_text)
                    <p class="cms-muted cms-media-alt">{{ $item->alt_text }}</p>
                @endif
                <div class="cms-media-meta">
                    <span>{{ number_format(($item->size_bytes ?? 0) / 1024, 1) }} KB</span>
                </div>
                @if ($canEdit)
                    <div class="cms-media-actions">
                        <a href="{{ route('cms.resource.edit', [$entity, $item->id]) }}">Edit alt text</a>
                        <form method="POST" action="{{ route('cms.resource.destroy', [$entity, $item->id]) }}" onsubmit="return confirm('Delete this file?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cms-link-btn">Delete</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="cms-panel cms-empty-state">
            <p>No media uploaded yet.</p>
            @if ($canEdit)
                <a href="{{ route('cms.resource.create', $entity) }}" class="cms-btn cms-btn-primary">Upload your first image</a>
            @endif
        </div>
    @endforelse
</div>

<div class="cms-pagination-wrap">
    {{ $items->links() }}
</div>

<script>
document.querySelectorAll('[data-copy]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        navigator.clipboard.writeText(btn.dataset.copy).then(function () {
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = 'Copy path'; }, 1500);
        });
    });
});
</script>
@endsection
