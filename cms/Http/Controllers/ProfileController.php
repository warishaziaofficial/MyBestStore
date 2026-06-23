<?php

namespace Cms\Http\Controllers;

use Cms\Support\CmsAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = CmsAuth::user();

        abort_if(! $user, 403);

        return view('cms::profile.show', [
            'user' => $user,
            'isAdmin' => CmsAuth::isAdmin(),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = CmsAuth::user();

        abort_if(! $user, 403);

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('cms.profile')->with('success', 'Password updated successfully.');
    }
}
