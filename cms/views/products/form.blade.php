@extends('cms::layouts.admin')

@section('title', ($item ? 'Edit' : 'Create').' '.$config['singular'])

@section('page_heading')
<div class="sf-page-banner__title-block">
    <h1 class="sf-page-title">{{ $item ? 'Edit' : 'Add' }} Product</h1>
    <p class="sf-page-subtitle">Pricing, inventory, images and storefront recommendations.</p>
</div>
@endsection

@section('page_actions')
<a href="{{ route('cms.products.index') }}" class="sf-btn sf-btn-outline sf-btn--sm">← Back</a>
@endsection

@section('content')
@php
    $formOptions = $formOptions ?? [];
    $basicFields = ['name', 'slug', 'category', 'sub_category', 'brand', 'badge'];
    $pricingFields = ['price', 'cost_price', 'old_price', 'stock'];
@endphp

<div class="sf-form-layout sf-form-layout--product sf-form-layout--standard">
    <form
        method="POST"
        action="{{ $item ? route('cms.resource.update', [$entity, data_get($item, $config['key'] ?? 'id')]) : route('cms.resource.store', $entity) }}"
        class="sf-form"
        enctype="multipart/form-data"
    >
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        <div class="sf-form-columns sf-product-columns">
            <div class="sf-form-columns__main sf-product-columns__main">
                <section class="sf-panel sf-form-section sf-product-card">
                    <header class="sf-form-section-head">
                        <h2>Basic details</h2>
                    </header>
                    <div class="sf-form-grid sf-form-grid--product-basic">
                        @foreach ($basicFields as $name)
                            @if (! isset($config['fields'][$name]))
                                @continue
                            @endif
                            @php $field = $config['fields'][$name]; @endphp
                            @if ($item && ($field['edit'] ?? true) === false)
                                @continue
                            @endif
                            @include('cms::crud._field', ['name' => $name, 'field' => $field, 'item' => $item, 'formOptions' => $formOptions, 'forceShort' => true])
                        @endforeach
                    </div>
                </section>

                @if (isset($config['fields']['description']))
                    <section class="sf-panel sf-form-section sf-product-card">
                        <header class="sf-form-section-head">
                            <h2>Description</h2>
                        </header>
                        <div class="sf-form-grid sf-form-grid--product-desc">
                            @include('cms::crud._field', [
                                'name' => 'description',
                                'field' => $config['fields']['description'],
                                'item' => $item,
                                'formOptions' => $formOptions,
                                'forceCompactDesc' => true,
                            ])
                        </div>
                    </section>
                @endif

                <section class="sf-panel sf-form-section sf-product-card">
                    <header class="sf-form-section-head">
                        <h2>Pricing &amp; inventory</h2>
                    </header>
                    <div class="sf-pricing-layout">
                        @foreach ($pricingFields as $name)
                            @if (! isset($config['fields'][$name]))
                                @continue
                            @endif
                            @php $field = $config['fields'][$name]; @endphp
                            @if ($item && ($field['edit'] ?? true) === false)
                                @continue
                            @endif
                            @include('cms::crud._field', ['name' => $name, 'field' => $field, 'item' => $item, 'formOptions' => $formOptions, 'forceShort' => true])
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="sf-form-columns__side sf-product-columns__side">
                <section class="sf-panel sf-form-section sf-product-card">
                    <header class="sf-form-section-head">
                        <h2>Product image</h2>
                    </header>
                    <div class="sf-form-grid sf-form-grid--product-media">
                        @include('cms::products._image-upload', ['config' => $config, 'item' => $item, 'asPanel' => true])
                    </div>
                </section>

                <section class="sf-panel sf-form-section sf-product-card">
                    <header class="sf-form-section-head">
                        <h2>Gallery images</h2>
                    </header>
                    <div class="sf-form-grid sf-form-grid--product-media">
                        @include('cms::products._gallery-manager', ['galleryImages' => $galleryImages ?? collect(), 'hideTitle' => true])
                    </div>
                </section>

                @if (isset($config['fields']['featured']))
                    <section class="sf-panel sf-form-section sf-product-card sf-product-card--featured">
                        @include('cms::products._visibility-bar', ['compact' => true])
                    </section>
                @endif
            </aside>
        </div>

        <section class="sf-panel sf-form-section sf-product-card sf-product-card--wide">
            <header class="sf-form-section-head">
                <h2>Product recommendations</h2>
                <p class="sf-form-section-desc">Search and pick related products for the storefront page.</p>
            </header>
            <div class="sf-form-grid sf-form-grid--relations-compact">
                @foreach ($productRelations as $type => $label)
                    @include('cms::products._relation-picker', [
                        'type' => $type,
                        'label' => $label,
                        'allProducts' => $allProducts,
                        'selectedRelations' => $selectedRelations,
                        'item' => $item,
                    ])
                @endforeach
            </div>
        </section>

        <div class="sf-form-actions sf-form-actions--product">
            <a href="{{ route('cms.products.index') }}" class="sf-btn sf-btn-outline sf-btn--sm">Cancel</a>
            <button type="submit" class="sf-btn sf-btn-primary sf-btn--sm">
                {{ $item ? 'Save product' : 'Create product' }}
            </button>
        </div>
    </form>
</div>

@if (collect($config['fields'])->contains(fn ($field) => ($field['type'] ?? '') === 'richtext'))
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@4.2.27/build/jodit.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jodit@4.2.27/build/jodit.min.js"></script>
    <script>
        document.querySelectorAll('.cms-richtext--compact').forEach(function (el) {
            new Jodit(el, {
                height: 88,
                minHeight: 72,
                toolbarAdaptive: false,
                toolbarSticky: false,
                showCharsCounter: false,
                showWordsCounter: false,
                showXPathInStatusbar: false,
                buttons: 'bold,italic,underline,|,link,|,undo,redo',
            });
        });
    </script>
@endif

<script src="{{ asset('assets/cms/js/product-form.js') }}?v={{ @filemtime(public_path('assets/cms/js/product-form.js')) ?: 1 }}" defer></script>
@endsection
