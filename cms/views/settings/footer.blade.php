@extends('cms::layouts.admin')

@section('title', 'Footer Settings')

@section('page_heading')
<div>
    <h1 class="sf-page-title">Footer Settings</h1>
    <p class="sf-page-subtitle">Manage footer text, social links, and newsletter copy shown on the storefront.</p>
</div>
@endsection

@section('content')
<div class="cms-panel">
    <form method="POST" action="{{ route('cms.settings.footer.update') }}" class="cms-form">
        @csrf
        @method('PUT')

        <label>
            Tagline
            <textarea name="tagline" rows="3" required>{{ old('tagline', $settings->tagline) }}</textarea>
        </label>

        <label>
            Website URL
            <input type="url" name="website_url" value="{{ old('website_url', $settings->website_url) }}" required>
        </label>

        <label>
            Instagram URL
            <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/...">
        </label>

        <label>
            Facebook URL
            <input type="url" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/...">
        </label>

        <label>
            Copyright text
            <input type="text" name="copyright_text" value="{{ old('copyright_text', $settings->copyright_text) }}" required>
        </label>

        <label>
            Newsletter heading
            <input type="text" name="newsletter_heading" value="{{ old('newsletter_heading', $settings->newsletter_heading) }}" required>
        </label>

        <label>
            Newsletter description
            <textarea name="newsletter_text" rows="2" required>{{ old('newsletter_text', $settings->newsletter_text) }}</textarea>
        </label>

        <button type="submit" class="cms-btn cms-btn-primary">Save Footer Settings</button>
    </form>
</div>
@endsection
