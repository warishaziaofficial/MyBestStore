@props(['order'])

@php
    use App\Support\OrderPresenter;

    $steps = OrderPresenter::trackingTimeline($order);
@endphp

<div {{ $attributes->merge(['class' => 'order-tracking-timeline']) }}>
    <ol class="order-tracking-steps">
        @foreach ($steps as $step)
            <li class="order-tracking-step order-tracking-step--{{ $step['state'] }}">
                <span class="order-tracking-step-marker" aria-hidden="true"></span>
                <div class="order-tracking-step-body">
                    <strong>{{ $step['label'] }}</strong>
                    @if ($step['state'] === 'current')
                        <span class="order-tracking-step-current">Current status</span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</div>
