@extends('layouts.admin')

@section('title', 'Courier Companies')
@section('heading', 'Courier Companies')

@section('content')
<div class="cms-card">
    <div class="cms-actions" style="margin-top:0;margin-bottom:1rem;">
        <a href="{{ route('admin.couriers.create') }}" class="mbs-btn mbs-btn-primary">Add Courier</a>
    </div>

    <table class="cms-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Tracking URL</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($couriers as $courier)
                <tr>
                    <td><strong>{{ $courier->name }}</strong></td>
                    <td>{{ $courier->contact_number ?: '—' }}</td>
                    <td>{{ $courier->tracking_url ?: '—' }}</td>
                    <td>{{ ucfirst($courier->status) }}</td>
                    <td>
                        <a href="{{ route('admin.couriers.edit', $courier) }}" class="cms-link">Edit</a>
                        <form action="{{ route('admin.couriers.destroy', $courier) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this courier?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cms-link" style="border:0;background:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No courier companies yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
