@php
    $fieldType = $field['type'] ?? 'text';
    $value = old($name, ($fieldType === 'password' ? '' : data_get($item, $name)));
    $required = ($field['required'] ?? false) && ! ($item && $fieldType === 'password');
    $label = ucfirst(str_replace('_', ' ', $name));
    $isFull = in_array($fieldType, ['richtext', 'textarea', 'image', 'file'], true)
        || in_array($name, ['body', 'description', 'notes', 'message'], true);
    $fieldId = 'sf-field-'.$name;
@endphp

<div @class(['sf-form-field', 'sf-form-field--full' => $isFull])>
    @if ($fieldType === 'checkbox')
        <div class="sf-form-switch">
            <input type="hidden" name="{{ $name }}" value="0">
            <input
                type="checkbox"
                id="{{ $fieldId }}"
                name="{{ $name }}"
                value="1"
                class="sf-form-switch-input"
                @checked($value)
            >
            <label for="{{ $fieldId }}" class="sf-form-switch-label">
                <span class="sf-form-switch-track" aria-hidden="true"></span>
                <span>
                    <strong>{{ $label }}</strong>
                    @if ($name === 'featured')
                        <span class="sf-form-hint">Show in Best Selling sections. Use Merchandising for New Arrivals.</span>
                    @endif
                </span>
            </label>
        </div>
    @else
        <label class="sf-form-label" for="{{ $fieldId }}">
            {{ $label }}
            @if ($required)
                <span class="sf-form-required">*</span>
            @endif
        </label>

        <div class="sf-form-control">
            @if ($fieldType === 'richtext')
                <textarea id="{{ $fieldId }}" class="cms-richtext sf-input sf-textarea" name="{{ $name }}" rows="12">{{ $value }}</textarea>
            @elseif ($fieldType === 'textarea')
                <textarea id="{{ $fieldId }}" class="sf-input sf-textarea" name="{{ $name }}" rows="{{ $name === 'body' ? 12 : 4 }}" @if($required) required @endif>{{ $value }}</textarea>
            @elseif ($fieldType === 'relation_select')
                <select id="{{ $fieldId }}" class="sf-input sf-select" name="{{ $name }}" @if($required) required @endif>
                    <option value="">Select…</option>
                    @foreach ($formOptions[$name] ?? [] as $option)
                        <option value="{{ $option['value'] }}" @selected((string) $value === (string) $option['value'])>{{ $option['label'] }}</option>
                    @endforeach
                </select>
            @elseif ($fieldType === 'select')
                <select id="{{ $fieldId }}" class="sf-input sf-select" name="{{ $name }}" @if($required) required @endif>
                    @foreach ($field['options'] as $option)
                        <option value="{{ $option }}" @selected((string) $value === (string) $option)>{{ $option ?: 'None' }}</option>
                    @endforeach
                </select>
            @elseif ($fieldType === 'image')
                @if ($value)
                    <div class="sf-form-image-preview">
                        <img src="{{ str_starts_with($value, 'http') ? $value : asset($value) }}" alt="Current image">
                    </div>
                @endif
                <div class="sf-form-file-row">
                    <input type="file" id="{{ $fieldId }}-file" class="sf-input sf-file" name="{{ $name }}_file" accept="image/*" @if($required && ! $value) required @endif>
                </div>
                @unless ($field['upload_only'] ?? false)
                    <input
                        type="text"
                        id="{{ $fieldId }}"
                        class="sf-input"
                        name="{{ $name }}"
                        value="{{ old($name, ($item ? data_get($item, $name) : '')) }}"
                        placeholder="Or paste path / URL (e.g. products/item.jpg)"
                    >
                @else
                    <input type="hidden" name="{{ $name }}" value="{{ old($name, ($item ? data_get($item, $name) : '')) }}">
                @endunless
            @elseif ($fieldType === 'file')
                <input type="file" id="{{ $fieldId }}" class="sf-input sf-file" name="{{ $name }}" accept="image/*" @if($required) required @endif>
            @else
                <input
                    type="{{ $fieldType }}"
                    id="{{ $fieldId }}"
                    class="sf-input"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    @if(isset($field['step'])) step="{{ $field['step'] }}" @endif
                    @if($required) required @endif
                    @if($item && $fieldType === 'password') placeholder="Leave blank to keep current password" @endif
                    @if(($field['readonly'] ?? false) || ($item && ($field['edit'] ?? true) === false)) readonly @endif
                >
            @endif
        </div>

        @if (! empty($field['hint']))
            <p class="sf-form-hint">{{ $field['hint'] }}</p>
        @elseif ($fieldType === 'richtext' && $name === 'description')
            <p class="sf-form-hint">Shown on the product page when filled.</p>
        @elseif ($fieldType === 'relation_select' && empty($formOptions[$name] ?? []))
            <p class="sf-form-hint">No options yet — add records in the related section first.</p>
        @elseif ($fieldType === 'image' && ! ($field['upload_only'] ?? false))
            <p class="sf-form-hint">Upload or pick a path from the <a href="{{ route('cms.resource.index', 'media') }}">Media Library</a>.</p>
        @endif
    @endif

    @error($name)
        <p class="sf-form-error">{{ $message }}</p>
    @enderror
    @error($name.'_file')
        <p class="sf-form-error">{{ $message }}</p>
    @enderror
</div>
