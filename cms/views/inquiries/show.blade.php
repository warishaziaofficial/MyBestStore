@extends('cms::layouts.admin')

@section('title', 'Inquiry #'.$inquiry->id)

@section('page_heading')
<div class="sf-page-head">
    <div>
        <h1 class="sf-page-title">Inquiry #{{ $inquiry->id }}</h1>
        <p class="sf-page-subtitle">Received {{ $inquiry->created_at?->format('M j, Y g:i A') }}</p>
    </div>
    <div class="cms-inline-actions">
        <a href="{{ route('cms.resource.index', 'inquiries') }}" class="cms-muted">Back to inquiries</a>
        @if ($canEdit ?? false)
            <form method="POST" action="{{ route('cms.resource.destroy', ['inquiries', $inquiry->id]) }}" onsubmit="return confirm('Delete this inquiry?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="cms-btn cms-btn-danger">Delete</button>
            </form>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="cms-detail-grid">
    <div class="cms-panel">
        <h2>Contact</h2>
        <dl class="cms-dl">
            <dt>Name</dt><dd>{{ $inquiry->name }}</dd>
            <dt>Email</dt><dd><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></dd>
            <dt>Phone</dt><dd>{{ $inquiry->phone ?: '—' }}</dd>
        </dl>
    </div>
    <div class="cms-panel">
        <h2>Subject</h2>
        <p>{{ $inquiry->subject ?: '—' }}</p>
    </div>
</div>

<div class="cms-panel">
    <h2>Message</h2>
    <p class="cms-message-body">{{ $inquiry->message }}</p>
</div>
@endsection
