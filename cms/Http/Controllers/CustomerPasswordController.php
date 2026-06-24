<?php

namespace Cms\Http\Controllers;

use App\Models\Customer as StorefrontCustomer;
use App\Support\PasswordResetNotifier;
use Cms\Models\Customer;
use Cms\Support\CmsAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CustomerPasswordController extends Controller
{
    public function index(): View
    {
        $this->requireEditor();

        return view('cms::customers.password-reset', [
            'customers' => Schema::hasTable('Customers')
                ? Customer::query()->orderBy('email')->get(['id', 'email', 'name', 'created_at'])
                : collect(),
        ]);
    }

    public function setPassword(Request $request): RedirectResponse
    {
        $this->requireEditor();

        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:Customers,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $customer = Customer::query()->findOrFail($data['customer_id']);
        $plainPassword = $data['password'];
        $hashed = Hash::make($plainPassword);

        $customer->update(['password' => $hashed]);

        $storeCustomer = StorefrontCustomer::query()->where('email', $customer->email)->first();

        if ($storeCustomer) {
            $storeCustomer->password = $plainPassword;
            $storeCustomer->save();
        }

        $admin = CmsAuth::user();

        PasswordResetNotifier::adminSetPassword(
            $customer->email,
            $plainPassword,
            $admin?->username,
        );

        return redirect()->route('cms.customers.password-reset')->with(
            'success',
            'Password updated for '.$customer->email.'. The customer and admin were notified by email.',
        );
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }
    }
}
