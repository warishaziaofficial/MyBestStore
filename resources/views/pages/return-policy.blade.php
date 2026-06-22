@extends('layouts.app')

@section('title', 'MyBestStore | Return Policy')

@section('content')
<x-static-page :title="$title" :description="$description ?? null">
    <p>We want you to shop with confidence at MyBestStore.pk. If a product does not meet your expectations, our return policy below explains when and how returns may be accepted.</p>

    <h2>Return Eligibility</h2>
    <p>Returns are accepted for eligible electronics and appliances that are unused, in original packaging, and include all accessories, manuals, and warranty cards supplied with the product. Certain items such as opened software, personalised products, or hygiene-sensitive accessories may not qualify for return.</p>

    <h2>Return Time Period</h2>
    <p>Most eligible products may be returned within <strong>7 days</strong> of delivery. Large appliances, home theatre systems, and special-order items may have different return windows depending on manufacturer policy. Our support team will confirm eligibility before you ship or hand over the item.</p>

    <h2>Product Condition</h2>
    <p>Returned items must be free from physical damage, installation marks, or signs of use beyond reasonable inspection. Missing components, altered serial numbers, or tampered warranty seals may result in partial refund or rejection of the return request.</p>

    <h2>Return Process</h2>
    <ol>
        <li>Contact MyBestStore support with your order number and reason for return.</li>
        <li>Receive return approval and pickup or drop-off instructions for your city.</li>
        <li>Once the product is inspected, refunds are processed to the original payment method or as store credit where applicable.</li>
    </ol>

    <h2>Exchanges</h2>
    <p>If you received a defective or incorrect item, we will arrange a replacement where stock is available. Please report such issues within 48 hours of delivery with photos or video where possible.</p>

    <h2>Need Help?</h2>
    <p>Visit our <a href="{{ route('contact') }}">Contact page</a> or email <a href="mailto:orders@mybeststore.pk">orders@mybeststore.pk</a> to start a return request.</p>
</x-static-page>
@endsection
