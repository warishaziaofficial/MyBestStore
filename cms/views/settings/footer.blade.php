@extends('cms::layouts.admin')

@section('title', 'Footer Settings')

@section('page_heading')
<div class="sf-page-banner__title-block">
    <h1 class="sf-page-title">Footer Settings</h1>
    <p class="sf-page-subtitle">Manage footer text, social links, and newsletter copy shown on the storefront.</p>
</div>
@endsection

@section('content')
<div class="sf-form-layout sf-form-layout--standard">
    <form method="POST" action="{{ route('cms.settings.footer.update') }}" class="sf-form">
        @csrf
        @method('PUT')

        <div class="sf-form-columns">
            <div class="sf-form-columns__main">
                <section class="sf-panel sf-form-section sf-form-card">
                    <header class="sf-form-section-head">
                        <h2>Footer copy</h2>
                    </header>
                    <div class="sf-form-grid sf-form-grid--content">
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-footer-tagline">Tagline</label>
                            <textarea id="sf-footer-tagline" class="sf-input sf-textarea" name="tagline" rows="2" required>{{ old('tagline', $settings->tagline) }}</textarea>
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-footer-copyright">Copyright text</label>
                            <input type="text" id="sf-footer-copyright" class="sf-input" name="copyright_text" value="{{ old('copyright_text', $settings->copyright_text) }}" required>
                        </div>
                    </div>
                </section>

                <section class="sf-panel sf-form-section sf-form-card">
                    <header class="sf-form-section-head">
                        <h2>Newsletter</h2>
                    </header>
                    <div class="sf-form-grid sf-form-grid--content">
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-footer-newsletter-heading">Newsletter heading</label>
                            <input type="text" id="sf-footer-newsletter-heading" class="sf-input" name="newsletter_heading" value="{{ old('newsletter_heading', $settings->newsletter_heading) }}" required>
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-footer-newsletter-text">Newsletter description</label>
                            <textarea id="sf-footer-newsletter-text" class="sf-input sf-textarea" name="newsletter_text" rows="2" required>{{ old('newsletter_text', $settings->newsletter_text) }}</textarea>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="sf-form-columns__side">
                <section class="sf-panel sf-form-section sf-form-card">
                    <header class="sf-form-section-head">
                        <h2>Links</h2>
                    </header>
                    <div class="sf-form-grid sf-form-grid--media">
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-footer-website">Website URL</label>
                            <input type="url" id="sf-footer-website" class="sf-input" name="website_url" value="{{ old('website_url', $settings->website_url) }}" required>
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-footer-instagram">Instagram URL</label>
                            <input type="url" id="sf-footer-instagram" class="sf-input" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/...">
                        </div>
                        <div class="sf-form-field sf-form-field--full">
                            <label class="sf-form-label" for="sf-footer-facebook">Facebook URL</label>
                            <input type="url" id="sf-footer-facebook" class="sf-input" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/...">
                        </div>
                    </div>
                </section>
            </aside>
        </div>

        <div class="sf-form-actions sf-form-actions--product">
            <button type="submit" class="sf-btn sf-btn-primary sf-btn--sm">Save footer settings</button>
        </div>
    </form>
</div>
@endsection
