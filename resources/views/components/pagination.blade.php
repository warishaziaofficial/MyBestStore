@php
    $paginator = $paginator ?? null;
@endphp

@if ($paginator && $paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $start = max(1, $current - 2);
        $end = min($last, $current + 2);
    @endphp
    <nav class="mbs-pagination" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="is-disabled">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">← Prev</a>
        @endif

        @if ($start > 1)
            <a href="{{ $paginator->url(1) }}">1</a>
            @if ($start > 2)
                <span class="is-disabled">…</span>
            @endif
        @endif

        @for ($page = $start; $page <= $end; $page++)
            @if ($page == $current)
                <span class="is-active">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}">{{ $page }}</a>
            @endif
        @endfor

        @if ($end < $last)
            @if ($end < $last - 1)
                <span class="is-disabled">…</span>
            @endif
            <a href="{{ $paginator->url($last) }}">{{ $last }}</a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Next →</a>
        @else
            <span class="is-disabled">Next →</span>
        @endif
    </nav>
@endif
