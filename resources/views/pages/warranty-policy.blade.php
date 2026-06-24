@extends('layouts.app')

@section('title', 'DigitalWares | Warranty Policy')

@section('content')
<x-static-page :title="$title" :description="$description ?? null">
    <p>DigitalWares.pk supplies genuine electronics backed by official manufacturer warranty wherever applicable. This page explains how warranty coverage works and how to submit a claim.</p>

    <h2>Official Manufacturer Warranty</h2>
    <p>Products sold on DigitalWares include manufacturer warranty as stated on the product page or invoice. Warranty duration and coverage vary by brand and category — for example, LED TVs, soundbars, air purifiers, and home appliances may each carry different terms.</p>
    <p>Authorized warranty applies to manufacturing defects under normal use. Warranty cards, serial numbers, and purchase proof must be retained for successful registration and claim processing.</p>

    <h2>What Is Covered</h2>
    <ul>
        <li>Manufacturing faults and component failure under normal operating conditions.</li>
        <li>Repairs or replacement performed by authorized service centres where applicable.</li>
        <li>Guidance from DigitalWares support to connect you with the correct brand service partner.</li>
    </ul>

    <h2>What Is Not Covered</h2>
    <ul>
        <li>Physical damage, liquid damage, power surge damage, or misuse.</li>
        <li>Unauthorized repairs, modified products, or missing serial labels.</li>
        <li>Consumables, accessories, and wear-and-tear items unless specified by the manufacturer.</li>
        <li>Damage caused by incorrect installation not performed by authorized personnel where required.</li>
    </ul>

    <h2>Warranty Claim Process</h2>
    <ol>
        <li>Contact DigitalWares with your order number, product model, and issue description.</li>
        <li>Provide proof of purchase and photos or videos if requested.</li>
        <li>We will direct you to the brand’s authorized service centre or arrange inspection where available.</li>
        <li>Repair, replacement, or resolution will follow the manufacturer’s official warranty terms.</li>
    </ol>

    <h2>Extended Support</h2>
    <p>Our team can help verify warranty status before you buy and assist with post-purchase claim coordination. For warranty enquiries, reach us via the <a href="{{ route('contact') }}">Contact page</a>.</p>
</x-static-page>
@endsection
