<?php

namespace Cms\Http\Controllers;

use Cms\Models\Inquiry;
use Cms\Support\CmsAuth;
use Illuminate\View\View;

class InquiryDetailController extends Controller
{
    public function show(int $id): View
    {
        $inquiry = Inquiry::query()->findOrFail($id);

        return view('cms::inquiries.show', [
            'inquiry' => $inquiry,
            'canEdit' => CmsAuth::canEdit(),
        ]);
    }
}
