@props([
    'title',
    'description' => null,
])

@include('components.page-hero', [
    'title' => $title,
    'description' => $description,
])

<section class="mbs-page-section mbs-page-section--muted">
    <div class="mbs-container">
        <article class="mbs-static-content">
            {{ $slot }}
        </article>
    </div>
</section>
