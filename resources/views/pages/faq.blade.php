@extends('layouts.app')

@section('title', 'MyBestStore | FAQ')

@section('content')
<x-static-page :title="$title" :description="$description ?? null">
    <p>Find quick answers to the most common questions about shopping, delivery, payments, and support at MyBestStore.pk.</p>

    <div class="mbs-static-faq" x-data="{ open: 0 }">
        @foreach (config('storefront.faqs', []) as $index => $faq)
            <div class="mbs-static-faq-item" :class="{ 'is-open': open === {{ $index }} }">
                <button
                    type="button"
                    class="mbs-static-faq-question"
                    :class="{ 'is-open': open === {{ $index }} }"
                    @click="open = open === {{ $index }} ? null : {{ $index }}"
                    :aria-expanded="open === {{ $index }}"
                >
                    <span>{{ $faq['q'] }}</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="mbs-static-faq-answer-wrap" :aria-hidden="open !== {{ $index }}">
                    <div class="mbs-static-faq-answer">
                        <p>{{ $faq['a'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <h2>Still need help?</h2>
    <p>Our support team is available Monday to Saturday. Visit the <a href="{{ route('contact') }}">Contact page</a>, call +92 300 1234567, or email <a href="mailto:orders@mybeststore.pk">orders@mybeststore.pk</a>.</p>
</x-static-page>
@endsection
