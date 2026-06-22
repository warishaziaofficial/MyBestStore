@extends('layouts.app')

@section('title', 'MyBestStore | Terms Of Service')

@section('content')
<x-static-page :title="$title" :description="$description ?? null">
    <p>Welcome to MyBestStore.pk. By accessing our website, placing an order, or creating an account, you agree to these Terms of Service. Please read them carefully before using our platform.</p>

    <h2>Ordering &amp; Payments</h2>
    <p>All orders placed on MyBestStore.pk are subject to product availability and confirmation. Prices are listed in Pakistani Rupees (PKR) and may change without prior notice. We accept Cash on Delivery, bank transfer, JazzCash, EasyPaisa, and major debit/credit cards where available.</p>
    <p>By submitting an order, you confirm that the billing and delivery information you provide is accurate. MyBestStore reserves the right to cancel or refuse any order at its discretion, including in cases of pricing errors, suspected fraud, or stock unavailability.</p>

    <h2>Account Usage</h2>
    <p>If you create an account, you are responsible for maintaining the confidentiality of your login credentials and for all activity under your account. You agree to provide accurate registration details and to update them when necessary.</p>
    <p>MyBestStore may suspend or terminate accounts that violate these terms, engage in abusive behaviour, or attempt to misuse promotions, checkout, or support services.</p>

    <h2>Website Usage</h2>
    <p>You may use our website for lawful personal shopping purposes only. You must not copy, scrape, reverse engineer, or redistribute site content, product listings, or pricing data without written permission.</p>
    <p>Product images, descriptions, and specifications are provided for customer guidance. While we strive for accuracy, minor variations in packaging, colour, or specifications may occur depending on manufacturer updates.</p>

    <h2>Delivery &amp; Risk</h2>
    <p>Estimated delivery timelines are provided at checkout and may vary by city. Risk of loss passes to you upon successful delivery to the address provided, subject to our Return Policy.</p>

    <h2>Contact</h2>
    <p>For questions about these terms, contact us at <a href="{{ route('contact') }}">orders@mybeststore.pk</a> or visit our <a href="{{ route('contact') }}">Contact page</a>.</p>
</x-static-page>
@endsection
