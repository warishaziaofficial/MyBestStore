@php
    $imageField = $config['fields']['image'] ?? null;
    $altField = $config['fields']['image_alt'] ?? null;
    $imageValue = old('image', $item ? data_get($item, 'image') : '');
    $altValue = old('image_alt', $item ? data_get($item, 'image_alt') : '');
    $imageRequired = ($imageField['required'] ?? false) && ! $imageValue;
    $asPanel = $asPanel ?? false;
@endphp

<div @class(['sf-product-image-box', 'sf-product-image-box--panel' => $asPanel]) data-image-upload>
    @unless ($asPanel)
        <p class="sf-product-image-box-title">Main image</p>
        <p class="sf-form-hint sf-product-image-box-desc">Cover photo for shop &amp; search.</p>
    @endunless

    <div
        class="sf-product-image-dropzone @if($imageValue) sf-product-image-dropzone--has-image @endif"
        data-image-dropzone
        data-file-input="sf-field-image-file"
    >
        <button
            type="button"
            class="sf-product-image-remove"
            data-image-remove
            title="Remove image"
            aria-label="Remove image"
            @if(! $imageValue) hidden @endif
        >&times;</button>
        @if ($imageValue)
            <img
                src="{{ str_starts_with($imageValue, 'http') ? $imageValue : asset($imageValue) }}"
                alt="Current image"
                class="sf-product-image-dropzone-preview"
                data-image-preview
            >
        @else
            <img alt="" class="sf-product-image-dropzone-preview" data-image-preview hidden>
        @endif
        <span class="sf-product-image-dropzone-text" data-image-dropzone-text @if($imageValue) hidden @endif>Drop image here or browse below</span>
    </div>

    <input type="hidden" name="remove_image" value="0" data-image-remove-flag>

    @unless ($asPanel)
        <label class="sf-form-label" for="sf-field-image-file">
            Image @if ($imageRequired)<span class="sf-form-required">*</span>@endif
        </label>
    @endunless
    <input
        type="file"
        id="sf-field-image-file"
        class="sf-input sf-file sf-input--compact"
        name="image_file"
        accept="image/*"
        @if($imageRequired) required @endif
    >

    @unless (($imageField['upload_only'] ?? false))
        <input
            type="text"
            id="sf-field-image"
            class="sf-input sf-input--compact"
            name="image"
            value="{{ $imageValue }}"
            placeholder="Path or URL"
        >
        @unless ($asPanel)
            <p class="sf-form-hint"><a href="{{ route('cms.resource.index', 'media') }}">Media Library</a></p>
        @else
            <p class="sf-form-hint"><a href="{{ route('cms.resource.index', 'media') }}">Media library</a></p>
        @endunless
    @else
        <input type="hidden" name="image" value="{{ $imageValue }}">
    @endunless

    @error('image')
        <p class="sf-form-error">{{ $message }}</p>
    @enderror
    @error('image_file')
        <p class="sf-form-error">{{ $message }}</p>
    @enderror

    @if ($altField)
        <label class="sf-form-label" for="sf-field-image_alt">
            Image alt @if ($altField['required'] ?? false)<span class="sf-form-required">*</span>@endif
        </label>
        <input
            type="text"
            id="sf-field-image_alt"
            class="sf-input sf-input--compact"
            name="image_alt"
            value="{{ $altValue }}"
            @if($altField['required'] ?? false) required @endif
        >
        @error('image_alt')
            <p class="sf-form-error">{{ $message }}</p>
        @enderror
    @endif
</div>
