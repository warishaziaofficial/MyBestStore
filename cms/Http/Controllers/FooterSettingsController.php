<?php

namespace Cms\Http\Controllers;

use Cms\Models\FooterSettings;
use Cms\Support\CmsAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FooterSettingsController extends Controller
{
    public function edit(): View
    {
        $this->requireEditor();

        return view('cms::settings.footer', [
            'settings' => FooterSettings::forEdit(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->requireEditor();

        $data = $request->validate([
            'tagline' => ['required', 'string'],
            'website_url' => ['required', 'url', 'max:500'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'copyright_text' => ['required', 'string', 'max:255'],
            'newsletter_heading' => ['required', 'string', 'max:255'],
            'newsletter_text' => ['required', 'string'],
        ]);

        FooterSettings::query()->updateOrCreate(['id' => 1], $data);

        return redirect()->route('cms.settings.footer')->with('success', 'Footer settings updated.');
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }
    }
}
