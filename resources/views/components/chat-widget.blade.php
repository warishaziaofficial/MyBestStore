@php $chat = \App\Support\ChatwootSettings::forStorefront(); @endphp
@if ($chat['enabled'] && $chat['base_url'] && ($chat['website_token'] || $chat['fallback_enabled']))
<script>
    window.chatwootSettings = {
        hideMessageBubble: false,
        position: @json($chat['position'] ?? 'right'),
        type: 'standard',
        launcherTitle: @json($chat['launcher_title']),
        welcomeTitle: @json($chat['welcome_title']),
        welcomeDescription: @json($chat['welcome_description']),
    };

    window.__MBS_CHATWOOT__ = {
        baseUrl: @json($chat['base_url']),
        websiteToken: @json($chat['website_token']),
        customer: @json($authCustomer ?? null),
        storeName: @json(config('app.name')),
        fallbackUrl: @json(route('contact')),
        fallbackEnabled: @json((bool) ($chat['fallback_enabled'] ?? true)),
        launcherTitle: @json($chat['launcher_title'] ?? 'Chat with us'),
    };
</script>
<script src="{{ asset('assets/js/chatwoot-widget.js') }}?v={{ filemtime(public_path('assets/js/chatwoot-widget.js')) }}" defer></script>
@endif
