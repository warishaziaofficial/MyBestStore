@extends('layouts.app')

@section('title', 'DigitalWares | Blog')

@section('content')
@include('components.page-hero', [
    'title' => 'Guides, Tips & News',
    'description' => 'Expert buying guides, product tips and the latest from DigitalWares.',
])

<section class="bg-secondary py-10">
    <div class="mbs-container grid max-w-none gap-8 lg:grid-cols-[280px_1fr]">
        @include('components.blog-sidebar')
        <div>
            <div class="grid gap-6 md:grid-cols-2">
                @forelse ($posts as $post)
                    @include('components.blog-card', ['post' => $post])
                @empty
                    <p class="rounded-2xl border border-border bg-white p-8 text-muted">No blog posts found.</p>
                @endforelse
            </div>
            @include('components.pagination')
        </div>
    </div>
</section>
@endsection
