@extends('layouts.app')

@section('title', 'DigitalWares | Privacy Policy')

@section('content')
<x-static-page :title="$title" :description="$description ?? null">
    <p>DigitalWares.pk respects your privacy. This policy explains what information we collect, how we use it, and the choices available to you when shopping with us.</p>

    <h2>Information We Collect</h2>
    <p>When you browse, register, or place an order, we may collect personal details such as your name, email address, phone number, billing address, delivery address, and order history. Payment details are processed securely through our payment partners and are not stored on our servers beyond what is required for transaction records.</p>

    <h2>How We Use Your Data</h2>
    <ul>
        <li>To process and deliver your orders across Pakistan.</li>
        <li>To send order confirmations, delivery updates, and support responses.</li>
        <li>To improve product recommendations, website performance, and customer service.</li>
        <li>To share promotional offers when you subscribe to our newsletter or opt in to marketing communications.</li>
    </ul>

    <h2>Order &amp; Support Information</h2>
    <p>We retain order records, warranty claims, and support conversations to help resolve issues, process returns, and comply with applicable business and tax requirements in Pakistan.</p>

    <h2>Cookies &amp; Analytics</h2>
    <p>Our website uses cookies and similar technologies to remember your preferences, keep your cart active, and understand how visitors use our store. You can control cookies through your browser settings, though some site features may not function correctly if cookies are disabled.</p>

    <h2>Data Sharing</h2>
    <p>We do not sell your personal information. We may share limited data with trusted courier partners, payment gateways, and service providers strictly for order fulfilment and customer support.</p>

    <h2>Your Rights</h2>
    <p>You may request access to, correction of, or deletion of your personal data by contacting our support team. For privacy-related enquiries, email <a href="mailto:orders@digitalwares.pk">orders@digitalwares.pk</a>.</p>
</x-static-page>
@endsection
