@php
    $features = [
        ['label' => 'Wireless Connectivity', 'image' => 'images/categories/mobile-accessories.jpg'],
        ['label' => 'Multi-Room Audio', 'image' => 'images/categories/home-theater.jpg'],
        ['label' => 'Smart Home Entertainment', 'image' => 'images/categories/led-tvs.jpg'],
        ['label' => 'Premium Electronics', 'image' => 'images/categories/sound-bars.jpg'],
        ['label' => 'High-Fidelity Sound', 'image' => 'images/categories/audio-equipment.jpg'],
        ['label' => 'Portable Audio', 'image' => 'products/showcase-soundbar.jpg'],
    ];
@endphp

<section class="mbs-feature-strip" aria-label="Premium features">
    <div class="mbs-feature-strip-track">
        <div class="mbs-feature-strip-marquee">
            @foreach ([1, 2] as $group)
                <div class="mbs-feature-strip-group" @if ($group === 2) aria-hidden="true" @endif>
                    @foreach ($features as $feature)
                        <div class="mbs-feature-item">
                            <span class="mbs-feature-label">{{ $feature['label'] }}</span>
                            <img
                                src="{{ asset($feature['image']) }}"
                                alt=""
                                class="mbs-feature-icon"
                                loading="lazy"
                            >
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</section>
