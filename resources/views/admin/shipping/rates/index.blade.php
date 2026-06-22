@extends('layouts.admin')

@section('title', 'Shipping Rates')
@section('heading', 'Shipping Rates')

@section('content')
<div class="cms-card">
    <div class="cms-actions" style="margin-top:0;margin-bottom:1rem;">
        <a href="{{ route('admin.shipping.rates.create') }}" class="mbs-btn mbs-btn-primary">Add Rate</a>
        <a href="{{ route('admin.shipping.zones.index') }}" class="cms-link">Manage Zones</a>
    </div>

    <table class="cms-table">
        <thead>
            <tr>
                <th>Zone</th>
                <th>Method</th>
                <th>Base Rate</th>
                <th>Weight Range</th>
                <th>Extra Rate</th>
                <th>Free Min</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rates as $rate)
                <tr>
                    <td>{{ $rate->zone?->name }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($rate->method_name)) }}</td>
                    <td>Rs {{ number_format((float) $rate->base_rate) }}</td>
                    <td>
                        @if ($rate->min_weight || $rate->max_weight)
                            {{ $rate->min_weight ?? '0' }}–{{ $rate->max_weight ?? '∞' }} kg
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $rate->extra_rate ? 'Rs '.number_format((float) $rate->extra_rate) : '—' }}</td>
                    <td>{{ $rate->free_shipping_min_amount ? 'Rs '.number_format((float) $rate->free_shipping_min_amount) : '—' }}</td>
                    <td>{{ ucfirst($rate->status) }}</td>
                    <td>
                        <a href="{{ route('admin.shipping.rates.edit', $rate) }}" class="cms-link">Edit</a>
                        <form action="{{ route('admin.shipping.rates.destroy', $rate) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this rate?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cms-link" style="border:0;background:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No shipping rates yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
