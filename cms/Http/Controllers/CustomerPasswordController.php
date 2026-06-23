<?php

namespace Cms\Http\Controllers;

use App\Support\CustomerPasswordReset;
use App\Support\EmailTemplateMailer;
use Cms\Models\Customer;
use Cms\Models\EmailTemplate;
use Cms\Support\CmsAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class CustomerPasswordController extends Controller
{
    public function index(): View
    {
        $this->requireEditor();

        return view('cms::customers.password-reset', [
            'customers' => Schema::hasTable('Customers')
                ? Customer::query()->orderBy('email')->get(['id', 'email', 'created_at'])
                : collect(),
        ]);
    }

    public function sendReset(Request $request): RedirectResponse
    {
        $this->requireEditor();

        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:Customers,id'],
        ]);

        $customer = Customer::query()->findOrFail($data['customer_id']);
        $token = CustomerPasswordReset::createToken($customer);
        $resetUrl = URL::route('customer.password.reset', ['token' => $token]);

        $template = Schema::hasTable('EmailTemplates')
            ? EmailTemplate::query()->where('slug', 'password_reset')->where('is_active', true)->first()
            : null;

        EmailTemplateMailer::sendOrFallback(
            'password_reset',
            $customer->email,
            ['reset_url' => $resetUrl],
            $template?->subject ?? 'Reset your MyBestStore password',
            $template?->body ?? "Reset your password using this link:\n{{reset_url}}"
        );

        return redirect()->route('cms.customers.password-reset')->with('success', 'Password reset email sent to '.$customer->email);
    }

    public function setPassword(Request $request): RedirectResponse
    {
        $this->requireEditor();

        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:Customers,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $customer = Customer::query()->findOrFail($data['customer_id']);
        $customer->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('cms.customers.password-reset')->with('success', 'Password updated for '.$customer->email);
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }
    }
}
