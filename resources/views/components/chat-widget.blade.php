@if (config('chatwoot.enabled') && config('chatwoot.base_url') && config('chatwoot.website_token'))
<script>
    window.chatwootSettings = {
        hideMessageBubble: false,
        position: @json(config('chatwoot.position', 'right')),
        type: 'standard',
        launcherTitle: @json(config('chatwoot.launcher_title')),
        welcomeTitle: @json(config('chatwoot.welcome_title')),
        welcomeDescription: @json(config('chatwoot.welcome_description')),
    };

    window.__MBS_CHATWOOT__ = {
        baseUrl: @json(config('chatwoot.base_url')),
        websiteToken: @json(config('chatwoot.website_token')),
        customer: @json($authCustomer ?? null),
        storeName: @json(config('app.name')),
    };
</script>
<script src="{{ asset('assets/js/chatwoot-widget.js') }}?v={{ filemtime(public_path('assets/js/chatwoot-widget.js')) }}" defer></script>
@endif
