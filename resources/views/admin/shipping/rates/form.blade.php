@extends('layouts.admin')

@section('title', $rate->exists ? 'Edit Shipping Rate' : 'Add Shipping Rate')
@section('heading', $rate->exists ? 'Edit Shipping Rate' : 'Add Shipping Rate')

@section('content')
<div class="cms-card">
    <form method="POST" action="{{ $rate->exists ? route('admin.shipping.rates.update', $rate) : route('admin.shipping.rates.store') }}">
        @csrf
        @if ($rate->exists)
            @method('PUT')
        @endif

        <div class="cms-field">
            <label for="shipping_zone_id">Zone</label>
            <select id="shipping_zone_id" name="shipping_zone_id" class="mbs-input" required>
                <option value="" disabled @selected(! old('shipping_zone_id', $rate->shipping_zone_id))>Select zone</option>
                @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}" @selected((int) old('shipping_zone_id', $rate->shipping_zone_id) === $zone->id)>{{ $zone->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="cms-field">
            <label for="method_name">Method name</label>
            <select id="method_name" name="method_name" class="mbs-input" required>
                <option value="standard_delivery" @selected(old('method_name', $rate->method_name) === 'standard_delivery')>Standard Delivery</option>
                <option value="express_delivery" @selected(old('method_name', $rate->method_name) === 'express_delivery')>Express Delivery</option>
            </select>
        </div>

        <div class="cms-field">
            <label for="base_rate">Base rate (PKR)</label>
            <input id="base_rate" type="number" step="0.01" min="0" name="base_rate" value="{{ old('base_rate', $rate->base_rate) }}" class="mbs-input" required>
        </div>

        <div class="cms-field">
            <label for="min_weight">Min weight (kg, optional)</label>
            <input id="min_weight" type="number" step="0.01" min="0" name="min_weight" value="{{ old('min_weight', $rate->min_weight) }}" class="mbs-input">
        </div>

        <div class="cms-field">
            <label for="max_weight">Max weight (kg, optional)</label>
            <input id="max_weight" type="number" step="0.01" min="0" name="max_weight" value="{{ old('max_weight', $rate->max_weight) }}" class="mbs-input">
        </div>

        <div class="cms-field">
            <label for="extra_rate">Extra rate (PKR, optional — e.g. express surcharge)</label>
            <input id="extra_rate" type="number" step="0.01" min="0" name="extra_rate" value="{{ old('extra_rate', $rate->extra_rate) }}" class="mbs-input">
        </div>

        <div class="cms-field">
            <label for="free_shipping_min_amount">Free shipping minimum amount (optional)</label>
            <input id="free_shipping_min_amount" type="number" step="0.01" min="0" name="free_shipping_min_amount" value="{{ old('free_shipping_min_amount', $rate->free_shipping_min_amount) }}" class="mbs-input">
        </div>

        <div class="cms-field">
            <label for="status">Status</label>
            <select id="status" name="status" class="mbs-input" required>
                <option value="active" @selected(old('status', $rate->status) === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $rate->status) === 'inactive')>Inactive</option>
            </select>
        </div>

        <div class="cms-actions">
            <button type="submit" class="mbs-btn mbs-btn-primary">Save Rate</button>
            <a href="{{ route('admin.shipping.rates.index') }}" class="cms-link">Cancel</a>
        </div>
    </form>
</div>
@endsection
