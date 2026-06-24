<?php

namespace Cms\Http\Controllers;

use App\Support\ChatwootSettings;
use Cms\Models\ChatSettings;
use Cms\Models\Inquiry;
use Cms\Support\CmsAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ChatSettingsController extends Controller
{
    public function edit(): View
    {
        $this->requireEditor();

        $settings = ChatSettings::forEdit();
        $chatOnline = ChatwootSettings::isOnline($settings->base_url);
        $inquiryCount = Schema::hasTable('Inquiries')
            ? Inquiry::query()->count()
            : 0;

        return view('cms::settings.chatbot', [
            'settings' => $settings,
            'chatOnline' => $chatOnline,
            'inquiryCount' => $inquiryCount,
            'tableReady' => Schema::hasTable('ChatSettings'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->requireEditor();

        $data = $request->validate([
            'is_enabled' => ['sometimes', 'boolean'],
            'base_url' => ['required', 'url', 'max:500'],
            'website_token' => ['nullable', 'string', 'max:255'],
            'chatwoot_admin_url' => ['nullable', 'url', 'max:500'],
            'crm_url' => ['nullable', 'url', 'max:500'],
            'dify_url' => ['nullable', 'url', 'max:500'],
            'chatdify_url' => ['nullable', 'url', 'max:500'],
            'launcher_title' => ['required', 'string', 'max:120'],
            'welcome_title' => ['required', 'string', 'max:255'],
            'welcome_description' => ['nullable', 'string'],
            'widget_position' => ['required', 'in:left,right'],
            'fallback_enabled' => ['sometimes', 'boolean'],
        ]);

        $data['is_enabled'] = $request->boolean('is_enabled');
        $data['fallback_enabled'] = $request->boolean('fallback_enabled');

        ChatSettings::query()->updateOrCreate(['id' => 1], $data);

        return redirect()->route('cms.settings.chatbot')->with('success', 'Chatbot settings saved. Storefront widget updated.');
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }
    }
}
