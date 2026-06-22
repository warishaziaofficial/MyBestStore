@extends('layouts.admin')

@section('title', 'Shipping Zones')
@section('heading', 'Shipping Zones')

@section('content')
<div class="cms-card">
    <div class="cms-actions" style="margin-top:0;margin-bottom:1rem;">
        <a href="{{ route('admin.shipping.zones.create') }}" class="mbs-btn mbs-btn-primary">Add Zone</a>
        <a href="{{ route('admin.shipping.rates.index') }}" class="cms-link">Manage Rates</a>
    </div>

    <table class="cms-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Country</th>
                <th>Province</th>
                <th>City</th>
                <th>Remote</th>
                <th>Status</th>
                <th>Rates</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($zones as $zone)
                <tr>
                    <td><strong>{{ $zone->name }}</strong></td>
                    <td>{{ $zone->country }}</td>
                    <td>{{ $zone->province ?: '—' }}</td>
                    <td>{{ $zone->city ?: '—' }}</td>
                    <td>{{ $zone->is_remote ? 'Yes' : 'No' }}</td>
                    <td>{{ ucfirst($zone->status) }}</td>
                    <td>{{ $zone->rates_count }}</td>
                    <td>
                        <a href="{{ route('admin.shipping.zones.edit', $zone) }}" class="cms-link">Edit</a>
                        <form action="{{ route('admin.shipping.zones.destroy', $zone) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this zone?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cms-link" style="border:0;background:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No shipping zones yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
