@php
    $galleryImages = $galleryImages ?? collect();
    $nextNewIndex = (int) old('gallery_new_next', max($galleryImages->count(), 1));
    $hideTitle = $hideTitle ?? false;
@endphp

<div class="sf-gallery-manager" data-gallery-manager>
    <div class="sf-gallery-manager-head">
        @unless ($hideTitle)
            <p class="sf-gallery-manager-title">Gallery images</p>
        @endunless
        <button type="button" class="sf-btn sf-btn-outline sf-btn--xs @if($hideTitle) sf-gallery-manager-add @endif" data-gallery-add>+ Add</button>
    </div>

    <div class="sf-gallery-list" data-gallery-list>
        @foreach ($galleryImages as $image)
            <div class="sf-gallery-row" data-gallery-row data-gallery-existing>
                <div class="sf-gallery-row-preview">
                    <img src="{{ str_starts_with($image->image, 'http') ? $image->image : asset($image->image) }}" alt="">
                </div>
                <div class="sf-gallery-row-fields">
                    <input type="hidden" name="gallery_existing[{{ $image->id }}][id]" value="{{ $image->id }}">
                    <label class="sf-form-label">Alt text</label>
                    <input
                        type="text"
                        class="sf-input sf-input--compact"
                        name="gallery_existing[{{ $image->id }}][alt_text]"
                        value="{{ old('gallery_existing.'.$image->id.'.alt_text', $image->alt_text) }}"
                        placeholder="Optional"
                    >
                    <label class="sf-form-label">Sort</label>
                    <input
                        type="number"
                        class="sf-input sf-input--compact"
                        name="gallery_existing[{{ $image->id }}][sort_order]"
                        value="{{ old('gallery_existing.'.$image->id.'.sort_order', $image->sort_order) }}"
                        min="0"
                    >
                </div>
                <button type="button" class="sf-gallery-row-remove" data-gallery-remove-existing="{{ $image->id }}" title="Remove">✕</button>
            </div>
        @endforeach
    </div>

    <template data-gallery-template>
        <div class="sf-gallery-row" data-gallery-row data-gallery-new>
            <div class="sf-gallery-row-preview sf-gallery-row-preview--empty">+</div>
            <div class="sf-gallery-row-fields">
                <label class="sf-form-label">Image</label>
                <input type="file" class="sf-input sf-file sf-input--compact" data-gallery-file accept="image/*">
                <input type="text" class="sf-input sf-input--compact" data-gallery-path placeholder="Or path / URL">
                <label class="sf-form-label">Alt text</label>
                <input type="text" class="sf-input sf-input--compact" data-gallery-alt placeholder="Optional">
                <label class="sf-form-label">Sort</label>
                <input type="number" class="sf-input sf-input--compact" data-gallery-sort value="0" min="0">
            </div>
            <button type="button" class="sf-gallery-row-remove" data-gallery-remove-new title="Remove">✕</button>
        </div>
    </template>

    <div data-gallery-remove-inputs></div>
    <input type="hidden" name="gallery_new_next" value="{{ $nextNewIndex }}" data-gallery-next-index>
</div>
