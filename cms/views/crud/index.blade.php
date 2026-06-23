@extends('cms::layouts.admin')

@section('title', $config['title'])

@section('page_heading')
@php
    $meta = $meta ?? \Cms\Support\ModuleMeta::for($entity);
@endphp
<div class="sf-page-head">
    <div>
        <h1 class="sf-page-title">{{ $meta['icon'] ?? '📄' }} {{ $config['title'] }}</h1>
        @if (! empty($meta['description']))
            <p class="sf-page-subtitle">{{ $meta['description'] }}</p>
        @endif
    </div>
    @if ($canEdit && empty($config['read_only']))
        <a href="{{ route('cms.resource.create', $entity) }}" class="sf-btn sf-btn-primary">+ Add {{ $config['singular'] }}</a>
    @endif
</div>
@endsection

@section('content')
@php
    $meta = $meta ?? \Cms\Support\ModuleMeta::for($entity);
@endphp
@if (! empty($meta['search_columns']))
    <form method="GET" action="{{ route('cms.resource.index', $entity) }}" class="sf-search">
        <input type="search" name="search" value="{{ $search ?? '' }}" placeholder="Search {{ strtolower($config['title']) }}...">
    </form>
@endif

@if ($entity === 'users')
    <div class="sf-panel" style="padding:14px 18px;margin-bottom:16px;">
        <strong>Roles:</strong>
        <span class="sf-pill sf-pill--gray">admin</span> full access ·
        <span class="sf-pill sf-pill--blue">editor</span> content ·
        <span class="sf-pill sf-pill--gray">viewer</span> read-only
    </div>
@endif

<div class="sf-panel sf-panel--flush">
    <div class="sf-table-wrap">
        <table class="sf-table">
            <thead>
                <tr>
                    @foreach ($config['columns'] as $column)
                        <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                    @endforeach
                    @if ($canEdit && empty($config['read_only']))
                        <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        @foreach ($config['columns'] as $column)
                            <td>
                                @if ($entity === 'media' && $column === 'path')
                                    <div class="sf-product-name">
                                        <img src="{{ asset(data_get($item, $column)) }}" alt="" class="sf-product-thumb">
                                        <span class="sf-subtext">{{ data_get($item, $column) }}</span>
                                    </div>
                                @elseif (in_array($column, ['image', 'image_alt'], true) && data_get($item, 'image'))
                                    <img src="{{ asset(data_get($item, 'image')) }}" alt="" class="sf-product-thumb">
                                @elseif (in_array($column, ['status', 'payment_status'], true))
                                    @php
                                        $status = data_get($item, $column);
                                        $pill = match ($status) {
                                            'approved', 'paid', 'delivered', 'confirmed' => 'sf-pill--green',
                                            'pending', 'processing' => 'sf-pill--gray',
                                            default => 'sf-pill--gray',
                                        };
                                    @endphp
                                    <span class="sf-pill {{ $pill }}">{{ strtoupper($status) }}</span>
                                @elseif ($column === 'payment_method')
                                    <span class="sf-pill sf-pill--blue">{{ strtoupper(data_get($item, $column, 'cod')) }}</span>
                                @elseif ($column === 'role')
                                    <span class="sf-pill sf-pill--blue">{{ data_get($item, $column) }}</span>
                                @elseif (in_array($column, ['featured', 'is_featured', 'is_active', 'is_published'], true))
                                    <span class="sf-pill sf-pill--{{ data_get($item, $column) ? 'green' : 'gray' }}">{{ data_get($item, $column) ? 'Yes' : 'No' }}</span>
                                @elseif ($column === 'price' || $column === 'total' || $column === 'line_total')
                                    Rs {{ number_format((int) data_get($item, $column)) }}
                                @else
                                    {{ \Illuminate\Support\Str::limit((string) data_get($item, $column), 80) }}
                                @endif
                            </td>
                        @endforeach
                        @if ($canEdit && empty($config['read_only']))
                            <td class="sf-actions">
                                @php $rowId = data_get($item, $config['key'] ?? 'id'); @endphp
                                @if (empty($config['upload_only']))
                                    <a href="{{ route('cms.resource.edit', [$entity, $rowId]) }}" class="sf-action sf-action--edit">Edit</a>
                                @endif
                                @if ($entity === 'orders')
                                    <a href="{{ route('cms.orders.show', $rowId) }}">View</a>
                                @endif
                                @if ($entity === 'inquiries')
                                    <a href="{{ route('cms.inquiries.show', $rowId) }}">View</a>
                                @endif
                                @if (! empty($config['moderation']))
                                    @if (data_get($item, 'status') !== 'approved')
                                        <form method="POST" action="{{ route('cms.moderate.status', [$entity, $rowId]) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="sf-action sf-action--edit">Approve</button>
                                        </form>
                                    @endif
                                    @if (data_get($item, 'status') !== 'rejected')
                                        <form method="POST" action="{{ route('cms.moderate.status', [$entity, $rowId]) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="sf-action sf-action--delete">Reject</button>
                                        </form>
                                    @endif
                                @endif
                                @if (empty($config['read_only']))
                                    <form method="POST" action="{{ route('cms.resource.destroy', [$entity, $rowId]) }}" onsubmit="return confirm('Delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sf-action sf-action--delete">Delete</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($config['columns']) + ($canEdit && empty($config['read_only']) ? 1 : 0) }}" class="sf-empty">
                            No {{ strtolower($config['title']) }} found.
                            @if ($canEdit && empty($config['read_only']))
                                <a href="{{ route('cms.resource.create', $entity) }}">Add your first {{ strtolower($config['singular']) }}</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="sf-pagination">{{ $items->links() }}</div>
@endsection
