<?php

namespace Cms\Http\Controllers;

use Cms\Models\User;
use Cms\Support\CmsAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (CmsAuth::check()) {
            return redirect('/cms');
        }

        return view('cms::auth.login', [
            'canRegister' => $this->canRegister(),
        ]);
    }

    public function showRegister(): View|RedirectResponse
    {
        if (CmsAuth::check()) {
            return redirect('/cms');
        }

        return view('cms::auth.register', [
            'canRegister' => $this->canRegister(),
            'isFirstAdmin' => User::count() === 0,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        if (CmsAuth::check()) {
            return redirect('/cms');
        }

        if (! $this->canRegister()) {
            return redirect()
                ->route('cms.login')
                ->withErrors(['email' => 'Admin registration is closed. Sign in or ask an existing admin to add your account.']);
        }

        $data = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:Users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $isFirst = User::count() === 0;

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $isFirst ? 'admin' : (config('cms.registration_role', 'editor')),
        ]);

        CmsAuth::login($user);

        return redirect('/cms')
            ->with('success', $isFirst
                ? 'Welcome! Your admin account is ready.'
                : 'Account created. You are now signed in.');
    }

    private function canRegister(): bool
    {
        if (User::count() === 0) {
            return true;
        }

        return (bool) config('cms.allow_signup', false);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        CmsAuth::login($user);
        $request->session()->regenerate();

        return redirect('/cms');
    }

    public function logout(): RedirectResponse
    {
        CmsAuth::logout();

        return redirect('/cms/login');
    }
}
