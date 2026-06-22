@extends('layouts.admin')

@section('title', $courier->exists ? 'Edit Courier' : 'Add Courier')
@section('heading', $courier->exists ? 'Edit Courier Company' : 'Add Courier Company')

@section('content')
<div class="cms-card">
    <form method="POST" action="{{ $courier->exists ? route('admin.couriers.update', $courier) : route('admin.couriers.store') }}">
        @csrf
        @if ($courier->exists)
            @method('PUT')
        @endif

        <div class="cms-field">
            <label for="name">Company name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $courier->name) }}" class="mbs-input" required>
        </div>

        <div class="cms-field">
            <label for="contact_number">Contact number</label>
            <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number', $courier->contact_number) }}" class="mbs-input">
        </div>

        <div class="cms-field">
            <label for="tracking_url">Tracking URL</label>
            <input id="tracking_url" type="text" name="tracking_url" value="{{ old('tracking_url', $courier->tracking_url) }}" class="mbs-input" placeholder="https://example.com/track/{tracking}">
            <small>Use <code>{tracking}</code> where the tracking number should be inserted.</small>
        </div>

        <div class="cms-field">
            <label for="status">Status</label>
            <select id="status" name="status" class="mbs-input" required>
                <option value="active" @selected(old('status', $courier->status) === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $courier->status) === 'inactive')>Inactive</option>
            </select>
        </div>

        <div class="cms-actions">
            <button type="submit" class="mbs-btn mbs-btn-primary">Save Courier</button>
            <a href="{{ route('admin.couriers.index') }}" class="cms-link">Cancel</a>
        </div>
    </form>
</div>
@endsection
