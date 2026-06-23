@extends('cms::layouts.admin')

@section('title', ($item ? 'Edit' : 'Create').' '.$config['singular'])

@section('page_heading')
@php
    $meta = $meta ?? \Cms\Support\ModuleMeta::for($entity);
@endphp
<div class="sf-page-head">
    <div>
        <h1 class="sf-page-title">{{ $meta['icon'] ?? '📄' }} {{ $item ? 'Edit' : 'Add' }} {{ $config['singular'] }}</h1>
        @if (! empty($meta['description']))
            <p class="sf-page-subtitle">{{ $meta['description'] }}</p>
        @endif
    </div>
    <a href="{{ route('cms.resource.index', $entity) }}" class="sf-btn sf-btn-outline">← Back to list</a>
</div>
@endsection

@section('content')
@php
    $hasUpload = collect($config['fields'])->contains(fn ($field) => in_array($field['type'] ?? '', ['image', 'file'], true));
    $formOptions = $formOptions ?? [];
    $meta = $meta ?? \Cms\Support\ModuleMeta::for($entity);

    $sections = [
        'General' => [],
        'Media' => [],
        'Content' => [],
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
@endphp

<div class="sf-form-layout">
    @if ($entity === 'media' && $item)
        <div class="sf-panel sf-form-media-banner">
            <img src="{{ asset($item->path) }}" alt="">
            <code>{{ $item->path }}</code>
        </div>
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

        @foreach ($sections as $sectionTitle => $sectionFields)
            <section class="sf-panel sf-form-section">
                <header class="sf-form-section-head">
                    <h2>{{ $sectionTitle }}</h2>
                </header>
                <div class="sf-form-grid">
                    @foreach ($sectionFields as $name => $field)
                        @if (! empty($field['virtual']) && ($field['type'] ?? '') === 'file')
                            <div class="sf-form-field sf-form-field--full">
                                <label class="sf-form-label" for="sf-field-{{ $name }}">Upload image</label>
                                <div class="sf-form-control">
                                    <input type="file" id="sf-field-{{ $name }}" class="sf-input sf-file" name="{{ $name }}" accept="image/*" @if(!$item && ($field['required'] ?? false)) required @endif>
                                </div>
                            </div>
                        @else
                            @include('cms::crud._field', ['name' => $name, 'field' => $field, 'item' => $item, 'formOptions' => $formOptions])
                        @endif
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="sf-form-actions">
            <a href="{{ route('cms.resource.index', $entity) }}" class="sf-btn sf-btn-outline">Cancel</a>
            <button type="submit" class="sf-btn sf-btn-primary">
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
                height: 360,
                toolbarAdaptive: false,
                buttons: 'bold,italic,underline,|,ul,ol,|,link,table,|,undo,redo,|,source',
            });
        });
    </script>
@endif
@endsection
