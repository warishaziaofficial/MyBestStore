<?php

namespace Cms\Http\Controllers;

use Cms\Support\CmsAuth;
use Cms\Support\ResourceRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    public function updateStatus(Request $request, string $entity, int $id): RedirectResponse
    {
        $this->requireEditor();

        if (! in_array($entity, ['reviews', 'ratings'], true)) {
            abort(404);
        }

        $config = ResourceRegistry::get($entity);
        $status = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ])['status'];

        $model = $config['model'];
        $item = $model::query()->findOrFail($id);
        $item->update(['status' => $status]);

        return redirect()
            ->route('cms.resource.index', $entity)
            ->with('success', ucfirst(rtrim($config['singular'], ' '))." marked as {$status}.");
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }
    }
}
