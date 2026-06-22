@extends('layouts.admin')

@section('title', $zone->exists ? 'Edit Shipping Zone' : 'Add Shipping Zone')
@section('heading', $zone->exists ? 'Edit Shipping Zone' : 'Add Shipping Zone')

@section('content')
<div class="cms-card">
    <form method="POST" action="{{ $zone->exists ? route('admin.shipping.zones.update', $zone) : route('admin.shipping.zones.store') }}">
        @csrf
        @if ($zone->exists)
            @method('PUT')
        @endif

        <div class="cms-field">
            <label for="name">Zone name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $zone->name) }}" class="mbs-input" required>
        </div>

        <div class="cms-field">
            <label for="country">Country</label>
            <input id="country" type="text" name="country" value="{{ old('country', $zone->country) }}" class="mbs-input" required>
        </div>

        <div class="cms-field">
            <label for="province">Province (optional)</label>
            <input id="province" type="text" name="province" value="{{ old('province', $zone->province) }}" class="mbs-input">
        </div>

        <div class="cms-field">
            <label for="city">City (optional)</label>
            <input id="city" type="text" name="city" value="{{ old('city', $zone->city) }}" class="mbs-input">
        </div>

        <div class="cms-field">
            <label>
                <input type="checkbox" name="is_remote" value="1" @checked(old('is_remote', $zone->is_remote))>
                Remote area
            </label>
        </div>

        <div class="cms-field">
            <label for="status">Status</label>
            <select id="status" name="status" class="mbs-input" required>
                <option value="active" @selected(old('status', $zone->status) === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $zone->status) === 'inactive')>Inactive</option>
            </select>
        </div>

        <div class="cms-actions">
            <button type="submit" class="mbs-btn mbs-btn-primary">Save Zone</button>
            <a href="{{ route('admin.shipping.zones.index') }}" class="cms-link">Cancel</a>
        </div>
    </form>
</div>
@endsection
