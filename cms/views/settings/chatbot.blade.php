@extends('cms::layouts.admin')

@section('title', 'Chatbot Integration')

@section('page_heading')
<div class="sf-page-banner__title-block">
    <h1 class="sf-page-title">Chatbot Integration</h1>
    <p class="sf-page-subtitle">Connect Chatwoot live chat to your storefront. Manage widget settings and open chat tools from CMS.</p>
</div>
@endsection

@section('content')
@if (! $tableReady)
    <div class="sf-alert sf-alert-warn">
        <strong>Setup required:</strong> Import <code>cms/ChatSettings.sql</code> in phpMyAdmin, then refresh this page.
    </div>
@endif

<div class="sf-kpi-row sf-kpi-row--compact">
    <div class="sf-kpi-card sf-kpi-card--{{ $chatOnline ? 'green' : 'orange' }}">
        <div class="sf-kpi-icon sf-kpi-icon--{{ $chatOnline ? 'green' : 'orange' }}">{{ $chatOnline ? '✓' : '!' }}</div>
        <div>
            <p class="sf-kpi-label">Chatwoot server</p>
            <p class="sf-kpi-value" style="font-size:1rem;">{{ $chatOnline ? 'Online' : 'Offline' }}</p>
        </div>
    </div>
    <div class="sf-kpi-card sf-kpi-card--blue">
        <div class="sf-kpi-icon sf-kpi-icon--blue">💬</div>
        <div>
            <p class="sf-kpi-label">Storefront widget</p>
            <p class="sf-kpi-value" style="font-size:1rem;">{{ $settings->is_enabled ? 'Enabled' : 'Disabled' }}</p>
        </div>
    </div>
    <div class="sf-kpi-card sf-kpi-card--pink">
        <div class="sf-kpi-icon sf-kpi-icon--pink">📩</div>
        <div>
            <p class="sf-kpi-label">Total inquiries</p>
            <p class="sf-kpi-value" style="font-size:1rem;">{{ number_format($inquiryCount) }}</p>
        </div>
    </div>
</div>

<div class="sf-form-layout sf-form-layout--standard">
    <form method="POST" action="{{ route('cms.settings.chatbot.update') }}" class="sf-form">
        @csrf
        @method('PUT')

        <div class="sf-form-columns">
            <div class="sf-form-columns__main">
                <section class="sf-panel sf-form-section sf-form-card">
                    <header class="sf-form-section-head">
                        <h2>Storefront widget</h2>
                        <p class="sf-form-section-desc">These settings control the chat bubble on every customer page.</p>
                    </header>
                    <div class="sf-form-grid sf-form-grid--content">
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-check">
                                <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $settings->is_enabled))>
                                <span>Enable chat widget on storefront</span>
                            </label>
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-check">
                                <input type="checkbox" name="fallback_enabled" value="1" @checked(old('fallback_enabled', $settings->fallback_enabled))>
                                <span>Show Contact button when Chatwoot is offline</span>
                            </label>
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-chat-base-url">Chatwoot URL</label>
                            <input type="url" id="sf-chat-base-url" class="sf-input" name="base_url" value="{{ old('base_url', $settings->base_url) }}" required placeholder="http://localhost:3000">
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-chat-token">Website inbox token</label>
                            <input type="text" id="sf-chat-token" class="sf-input" name="website_token" value="{{ old('website_token', $settings->website_token) }}" placeholder="From Chatwoot → Settings → Inboxes → Configuration">
                        </div>
                        <div class="sf-form-field">
                            <label class="sf-form-label" for="sf-chat-launcher">Launcher label</label>
                            <input type="text" id="sf-chat-launcher" class="sf-input" name="launcher_title" value="{{ old('launcher_title', $settings->launcher_title) }}" required>
                        </div>
                        <div class="sf-form-field">
                            <label class="sf-form-label" for="sf-chat-position">Widget position</label>
                            <select id="sf-chat-position" class="sf-input sf-select" name="widget_position" required>
                                <option value="right" @selected(old('widget_position', $settings->widget_position) === 'right')>Bottom right</option>
                                <option value="left" @selected(old('widget_position', $settings->widget_position) === 'left')>Bottom left</option>
                            </select>
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-chat-welcome-title">Welcome title</label>
                            <input type="text" id="sf-chat-welcome-title" class="sf-input" name="welcome_title" value="{{ old('welcome_title', $settings->welcome_title) }}" required>
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-chat-welcome-desc">Welcome message</label>
                            <textarea id="sf-chat-welcome-desc" class="sf-input sf-textarea" name="welcome_description" rows="3">{{ old('welcome_description', $settings->welcome_description) }}</textarea>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="sf-form-columns__side">
                <section class="sf-panel sf-form-section sf-form-card">
                    <header class="sf-form-section-head">
                        <h2>Connected services</h2>
                        <p class="sf-form-section-desc">Quick links to your chatbot stack (Docker services).</p>
                    </header>
                    <div class="sf-form-grid sf-form-grid--media">
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-chat-admin-url">Chatwoot admin URL</label>
                            <input type="url" id="sf-chat-admin-url" class="sf-input" name="chatwoot_admin_url" value="{{ old('chatwoot_admin_url', $settings->chatwoot_admin_url) }}" placeholder="http://localhost:3000">
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-chat-crm-url">CRM URL</label>
                            <input type="url" id="sf-chat-crm-url" class="sf-input" name="crm_url" value="{{ old('crm_url', $settings->crm_url) }}" placeholder="http://localhost:3002">
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-chat-dify-url">Dify URL</label>
                            <input type="url" id="sf-chat-dify-url" class="sf-input" name="dify_url" value="{{ old('dify_url', $settings->dify_url) }}" placeholder="http://localhost:8080">
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-chat-bridge-url">Chatdify bridge URL</label>
                            <input type="url" id="sf-chat-bridge-url" class="sf-input" name="chatdify_url" value="{{ old('chatdify_url', $settings->chatdify_url) }}" placeholder="http://localhost:8000">
                        </div>
                    </div>
                    <div class="sf-quick-links" style="margin-top:14px;">
                        <a href="{{ $settings->chatwoot_admin_url ?: $settings->base_url }}" target="_blank" rel="noopener" class="sf-quick-link">Open Chatwoot ↗</a>
                        @if ($settings->crm_url)
                            <a href="{{ $settings->crm_url }}" target="_blank" rel="noopener" class="sf-quick-link">Open CRM ↗</a>
                        @endif
                        <a href="{{ route('cms.resource.index', 'inquiries') }}" class="sf-quick-link">View Inquiries</a>
                        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="sf-quick-link">Preview storefront ↗</a>
                    </div>
                </section>

                <section class="sf-panel sf-form-section sf-form-card">
                    <header class="sf-form-section-head">
                        <h2>How it works</h2>
                    </header>
                    <ul class="sf-help-list">
                        <li>Customer opens chat on storefront → Chatwoot inbox</li>
                        <li>Chatwoot → Chatdify → Dify AI replies</li>
                        <li>Qualified leads → CRM via n8n</li>
                        <li>Contact form submissions → CMS Inquiries</li>
                    </ul>
                    @unless ($chatOnline)
                        <p class="sf-form-section-desc" style="margin-top:12px;">Chatwoot is offline. Install Docker and run <code>start-all.bat</code> from the chatbot folder, or enable the Contact fallback above.</p>
                    @endunless
                </section>
            </aside>
        </div>

        <div class="sf-form-actions sf-form-actions--product">
            <button type="submit" class="sf-btn sf-btn-primary sf-btn--sm">Save chatbot settings</button>
        </div>
    </form>
</div>
@endsection
