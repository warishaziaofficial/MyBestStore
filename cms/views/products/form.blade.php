@extends('cms::layouts.admin')

@section('title', ($item ? 'Edit' : 'Create').' '.$config['singular'])

@section('page_heading')
<div class="sf-page-head">
    <div>
        <h1 class="sf-page-title">📦 {{ $item ? 'Edit' : 'Add' }} Product</h1>
        <p class="sf-page-subtitle">Pricing, inventory, images and storefront recommendations.</p>
    </div>
    <a href="{{ route('cms.products.index') }}" class="sf-btn sf-btn-outline">← Back to products</a>
</div>
@endsection

@section('content')
@php
    $hasUpload = collect($config['fields'])->contains(fn ($field) => in_array($field['type'] ?? '', ['image', 'file'], true));
    $formOptions = $formOptions ?? [];

    $groups = [
        'Basic details' => ['name', 'slug', 'category', 'sub_category', 'brand', 'badge'],
        'Pricing & inventory' => ['price', 'cost_price', 'old_price', 'stock'],
        'Description' => ['description'],
        'Visibility' => ['featured'],
    ];
@endphp

<div class="sf-form-layout">
    <form
        method="POST"
        action="{{ $item ? route('cms.resource.update', [$entity, data_get($item, $config['key'] ?? 'id')]) : route('cms.resource.store', $entity) }}"
        class="sf-form"
        @if ($hasUpload) enctype="multipart/form-data" @endif
    >
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        @foreach ($groups as $title => $fieldNames)
            @php
                $sectionFields = collect($fieldNames)
                    ->filter(fn ($name) => isset($config['fields'][$name]))
                    ->mapWithKeys(fn ($name) => [$name => $config['fields'][$name]])
                    ->all();
            @endphp
            @if ($sectionFields !== [])
                <section class="sf-panel sf-form-section">
                    <header class="sf-form-section-head">
                        <h2>{{ $title }}</h2>
                    </header>
                    <div class="sf-form-grid">
                        @foreach ($sectionFields as $name => $field)
                            @if ($item && ($field['edit'] ?? true) === false)
                                @continue
                            @endif
                            @include('cms::crud._field', ['name' => $name, 'field' => $field, 'item' => $item, 'formOptions' => $formOptions])
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

        <section class="sf-panel sf-form-section">
            <header class="sf-form-section-head">
                <h2>Main image</h2>
                <p class="sf-form-section-desc">Cover photo used on shop, cart and search results. For extra photos (gallery thumbnails on the product page), use <a href="{{ route('cms.resource.index', 'product-images') }}">Catalog → Product Gallery</a> after saving this product.</p>
            </header>
            <div class="sf-form-grid">
                @foreach (['image', 'image_alt'] as $name)
                    @if (isset($config['fields'][$name]))
                        @include('cms::crud._field', ['name' => $name, 'field' => $config['fields'][$name], 'item' => $item, 'formOptions' => $formOptions])
                    @endif
                @endforeach
            </div>
        </section>

        <section class="sf-panel sf-form-section">
            <header class="sf-form-section-head">
                <h2>Product recommendations</h2>
                <p class="sf-form-section-desc">Pick other products to show on this product’s storefront page — search and tick each one (not typing names manually).</p>
            </header>
            <div class="sf-form-grid sf-form-grid--relations">
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

        <div class="sf-form-actions">
            <a href="{{ route('cms.products.index') }}" class="sf-btn sf-btn-outline">Cancel</a>
            <button type="submit" class="sf-btn sf-btn-primary">
                {{ $item ? 'Save product' : 'Create product' }}
            </button>
        </div>
    </form>
</div>

@if (collect($config['fields'])->contains(fn ($field) => ($field['type'] ?? '') === 'richtext'))
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@4.2.27/build/jodit.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jodit@4.2.27/build/jodit.min.js"></script>
    <script>
        document.querySelectorAll('.cms-richtext').forEach(function (el) {
            new Jodit(el, {
                height: 360,
                toolbarAdaptive: false,
                buttons: 'bold,italic,underline,|,ul,ol,|,link,table,|,undo,redo,|,source',
            });
        });
    </script>
@endif

<script>
document.querySelectorAll('[data-relation-search]').forEach(function (input) {
    var picker = input.closest('[data-relation-picker]');
    var items = picker.querySelectorAll('[data-relation-item]');

    input.addEventListener('input', function () {
        var query = input.value.trim().toLowerCase();
        items.forEach(function (item) {
            var name = item.getAttribute('data-name') || '';
            item.hidden = query !== '' && !name.includes(query);
        });
    });
});
</script>
@endsection
