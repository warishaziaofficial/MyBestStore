@extends('layouts.app')

@section('title', 'DigitalWares | Product Guides')

@section('content')
<x-static-page :title="$title" :description="$description ?? null">
    <p>Not sure which product fits your room, budget, or lifestyle? These quick buying guides from the DigitalWares team will help you choose with confidence.</p>

    <h2>LED TVs</h2>
    <p>Consider screen size based on viewing distance — larger rooms benefit from 55" and above, while bedrooms often suit 43"–50" models. Look for 4K resolution, HDR support, and smart features if you stream regularly. Check HDMI port count for soundbars, consoles, and set-top boxes.</p>

    <h2>Soundbars &amp; Home Audio</h2>
    <p>For cinematic sound in smaller spaces, a compact soundbar with a wireless subwoofer is ideal. Dolby Atmos models add height channels for a more immersive experience. Match connectivity options (HDMI ARC/eARC, optical, Bluetooth) with your TV and room layout before buying.</p>

    <h2>Air Purifiers</h2>
    <p>Choose coverage based on room size in square feet. HEPA filtration, CADR ratings, and low noise levels matter for bedrooms and living areas. If allergies or urban air quality are a concern, look for models with multi-stage filtration and easy filter replacement.</p>

    <h2>Accessories &amp; Add-ons</h2>
    <p>Wall mounts, HDMI cables, surge protectors, and TV trolleys can complete your setup safely. Prioritize certified cables for 4K/HDR content and ensure mounts support your TV’s size and weight rating. Gaming and work accessories should match your device ports and power requirements.</p>

    <h2>Need Personal Advice?</h2>
    <p>Browse our <a href="{{ route('shop') }}">shop</a>, compare products side by side, or contact our team through the <a href="{{ route('contact') }}">Contact page</a> for tailored recommendations based on your room and budget.</p>
</x-static-page>
@endsection
