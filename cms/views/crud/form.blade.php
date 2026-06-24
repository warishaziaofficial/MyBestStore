@extends('cms::layouts.admin')

@section('title', ($item ? 'Edit' : 'Create').' '.$config['singular'])

@section('page_heading')
@php
    $meta = $meta ?? \Cms\Support\ModuleMeta::for($entity);
@endphp
<div class="sf-page-banner__title-block">
    <h1 class="sf-page-title">{{ $item ? 'Edit' : 'Add' }} {{ $config['singular'] }}</h1>
    @if (! empty($meta['description']))
        <p class="sf-page-subtitle">{{ $meta['description'] }}</p>
    @endif
</div>
@endsection

@section('page_actions')
<a href="{{ route('cms.resource.index', $entity) }}" class="sf-btn sf-btn-outline sf-btn--sm">← Back</a>
@endsection

@section('content')
@php
    $hasUpload = collect($config['fields'])->contains(fn ($field) => in_array($field['type'] ?? '', ['image', 'file'], true));
    $formOptions = $formOptions ?? [];

    $sections = [
        'General' => [],
        'Content' => [],
        'Media' => [],
        'Settings' => [],
    ];

    foreach ($config['fields'] as $name => $field) {
        if (! empty($field['virtual']) || ! empty($field['hidden'])) {
            if (! empty($field['virtual']) && ($field['type'] ?? '') === 'file') {
                $sections['Media'][$name] = $field;
            }
            continue;
        }
        if ($item && ($field['edit'] ?? true) === false) {
            continue;
        }
        if ($item && ! empty($field['create'])) {
            continue;
        }
        if (! $item && ($field['edit'] ?? true) === false) {
            continue;
        }

        $type = $field['type'] ?? 'text';
        if (in_array($type, ['image', 'file'], true)) {
            $sections['Media'][$name] = $field;
        } elseif (in_array($type, ['richtext', 'textarea'], true) || in_array($name, ['body', 'description', 'notes', 'message'], true)) {
            $sections['Content'][$name] = $field;
        } elseif ($type === 'checkbox') {
            $sections['Settings'][$name] = $field;
        } else {
            $sections['General'][$name] = $field;
        }
    }

    $sections = array_filter($sections, fn (array $fields) => $fields !== []);
    $leftSections = array_filter([
        'General' => $sections['General'] ?? [],
        'Content' => $sections['Content'] ?? [],
    ], fn (array $fields) => $fields !== []);
    $rightSections = array_filter([
        'Media' => $sections['Media'] ?? [],
        'Settings' => $sections['Settings'] ?? [],
    ], fn (array $fields) => $fields !== []);
    $useColumns = $leftSections !== [] && $rightSections !== [];
    $singleSections = $useColumns ? [] : $sections;

    $gridClass = fn (string $title) => match ($title) {
        'General' => 'sf-form-grid sf-form-grid--compact',
        'Content' => 'sf-form-grid sf-form-grid--content',
        'Media' => 'sf-form-grid sf-form-grid--media',
        'Settings' => 'sf-form-grid sf-form-grid--settings',
        default => 'sf-form-grid',
    };
@endphp

<div class="sf-form-layout sf-form-layout--standard">
    @if ($entity === 'media' && $item)
        <section class="sf-panel sf-form-section sf-form-card sf-form-card--wide">
            <header class="sf-form-section-head">
                <h2>Current file</h2>
            </header>
            <div class="sf-form-media-banner">
                <img src="{{ asset($item->path) }}" alt="">
                <code>{{ $item->path }}</code>
            </div>
        </section>
    @endif

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

        @if ($useColumns)
            <div class="sf-form-columns">
                <div class="sf-form-columns__main">
                    @foreach ($leftSections as $sectionTitle => $sectionFields)
                        <section class="sf-panel sf-form-section sf-form-card">
                            <header class="sf-form-section-head">
                                <h2>{{ $sectionTitle }}</h2>
                            </header>
                            <div class="{{ $gridClass($sectionTitle) }}">
                                @foreach ($sectionFields as $name => $field)
                                    @include('cms::crud._field', ['name' => $name, 'field' => $field, 'item' => $item, 'formOptions' => $formOptions, 'forceShort' => $sectionTitle === 'General'])
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>

                <aside class="sf-form-columns__side">
                    @foreach ($rightSections as $sectionTitle => $sectionFields)
                        <section class="sf-panel sf-form-section sf-form-card">
                            <header class="sf-form-section-head">
                                <h2>{{ $sectionTitle }}</h2>
                            </header>
                            <div class="{{ $gridClass($sectionTitle) }}">
                                @foreach ($sectionFields as $name => $field)
                                    @if (! empty($field['virtual']) && ($field['type'] ?? '') === 'file')
                                        <div class="sf-form-field sf-form-field--full">
                                            <label class="sf-form-label" for="sf-field-{{ $name }}">Upload image</label>
                                            <div class="sf-form-control">
                                                <input type="file" id="sf-field-{{ $name }}" class="sf-input sf-file" name="{{ $name }}" accept="image/*" @if(!$item && ($field['required'] ?? false)) required @endif>
                                            </div>
                                        </div>
                                    @else
                                        @include('cms::crud._field', ['name' => $name, 'field' => $field, 'item' => $item, 'formOptions' => $formOptions, 'forceShort' => $sectionTitle === 'General'])
                                    @endif
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </aside>
            </div>
        @else
            @foreach ($singleSections as $sectionTitle => $sectionFields)
                <section class="sf-panel sf-form-section sf-form-card">
                    <header class="sf-form-section-head">
                        <h2>{{ $sectionTitle }}</h2>
                    </header>
                    <div class="{{ $gridClass($sectionTitle) }}">
                        @foreach ($sectionFields as $name => $field)
                            @if (! empty($field['virtual']) && ($field['type'] ?? '') === 'file')
                                <div class="sf-form-field sf-form-field--full">
                                    <label class="sf-form-label" for="sf-field-{{ $name }}">Upload image</label>
                                    <div class="sf-form-control">
                                        <input type="file" id="sf-field-{{ $name }}" class="sf-input sf-file" name="{{ $name }}" accept="image/*" @if(!$item && ($field['required'] ?? false)) required @endif>
                                    </div>
                                </div>
                            @else
                                @include('cms::crud._field', ['name' => $name, 'field' => $field, 'item' => $item, 'formOptions' => $formOptions, 'forceShort' => ($sectionTitle === 'General')])
                            @endif
                        @endforeach
                    </div>
                </section>
            @endforeach
        @endif

        <div class="sf-form-actions sf-form-actions--product">
            <a href="{{ route('cms.resource.index', $entity) }}" class="sf-btn sf-btn-outline sf-btn--sm">Cancel</a>
            <button type="submit" class="sf-btn sf-btn-primary sf-btn--sm">
                {{ $item ? 'Save changes' : 'Create '.$config['singular'] }}
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
                height: 280,
                toolbarAdaptive: false,
                buttons: 'bold,italic,underline,|,ul,ol,|,link,table,|,undo,redo,|,source',
            });
        });
    </script>
@endif
@endsection
