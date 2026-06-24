@php

    $featuredValue = old('featured', $item ? (bool) data_get($item, 'featured') : false);

    $compact = $compact ?? false;

@endphp



<div @class([

    'sf-product-visibility',

    'sf-panel' => ! $compact,

    'sf-product-visibility--sidebar' => $compact,

])>

    <div class="sf-product-visibility__info">

        <span class="sf-product-visibility__title">Featured product</span>

        <span class="sf-product-visibility__hint">Show in Best Selling sections. Use Merchandising for New Arrivals.</span>

    </div>

    <div class="sf-form-switch sf-form-switch--compact">

        <input type="hidden" name="featured" value="0">

        <input

            type="checkbox"

            id="sf-field-featured"

            name="featured"

            value="1"

            class="sf-form-switch-input"

            @checked($featuredValue)

        >

        <label for="sf-field-featured" class="sf-form-switch-label sf-form-switch-label--compact">

            <span class="sf-form-switch-track" aria-hidden="true"></span>

            <span class="sf-product-visibility__switch-text">{{ $featuredValue ? 'On' : 'Off' }}</span>

        </label>

    </div>

</div>

