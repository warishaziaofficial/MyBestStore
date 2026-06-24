<?php

namespace App\Http\Controllers;

use App\Models\Customer as StorefrontCustomer;
use App\Support\CustomerPasswordReset;
use Cms\Models\Customer as CmsCustomer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class CustomerPasswordResetController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        $email = CustomerPasswordReset::findEmailByToken($token);

        if (! $email) {
            return redirect()->route('home')->with('login_required', 'This reset link is invalid or has expired. Request a new one from Sign In.');
        }

        return view('pages.customer-reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function update(Request $request, string $token): RedirectResponse
    {
        $email = CustomerPasswordReset::findEmailByToken($token);

        if (! $email) {
            return back()->withErrors(['password' => 'This reset link is invalid or has expired.']);
        }

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $hashed = Hash::make($data['password']);

        if (Schema::hasTable('Customers')) {
            CmsCustomer::query()->where('email', $email)->update(['password' => $hashed]);
        }

        $storeCustomer = StorefrontCustomer::query()->where('email', $email)->first();

        if ($storeCustomer) {
            $storeCustomer->password = $data['password'];
            $storeCustomer->save();
        }

        CustomerPasswordReset::delete($email);

        if ($storeCustomer) {
            Auth::guard('customer')->login($storeCustomer);
        }

        return redirect()->route('home')->with('customer_auth_success', 'Password updated. You can sign in with your new password.');
    }
}
