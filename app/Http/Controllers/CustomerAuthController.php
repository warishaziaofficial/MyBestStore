<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CrmContactSync;
use App\Services\OrderTrackingService;
use App\Support\PasswordResetNotifier;
use Cms\Models\Customer as CmsCustomer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class CustomerAuthController extends Controller
{
    public function __construct(
        private readonly OrderTrackingService $tracking,
        private readonly CrmContactSync $crmSync,
    ) {}

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $customer = Customer::query()->where('email', $validated['email'])->first();

        if (! $customer || ! $customer->password || ! Hash::check($validated['password'], $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 422);
        }

        Auth::guard('customer')->login($customer, $request->boolean('remember'));

        return response()->json([
            'success' => true,
            'message' => 'Welcome back, '.$customer->name.'!',
            'customer' => $this->customerPayload($customer),
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $customer = Customer::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
        ]);

        $this->crmSync->syncSignup($customer);

        Auth::guard('customer')->login($customer);

        return response()->json([
            'success' => true,
            'message' => 'Your account has been created. Welcome to DigitalWares!',
            'customer' => $this->customerPayload($customer),
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($validated['email']));
        $name = null;
        $exists = false;

        if (Schema::hasTable('Customers')) {
            $cmsCustomer = CmsCustomer::query()->where('email', $email)->first();

            if ($cmsCustomer) {
                $exists = true;
                $name = $cmsCustomer->name;
            }
        }

        if (! $exists) {
            $customer = Customer::query()->where('email', $email)->first();

            if ($customer) {
                $exists = true;
                $name = $customer->name;
            }
        }

        if ($exists) {
            PasswordResetNotifier::forgotPasswordRequested($email, $name);
        }

        return response()->json([
            'success' => true,
            'message' => 'If this email exists, reset instructions will be sent.',
        ]);
    }

    public function trackOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:64'],
            'phone' => ['required_without:email', 'nullable', 'string', 'max:30'],
            'email' => ['required_without:phone', 'nullable', 'email', 'max:150'],
        ]);

        $order = $this->tracking->findForCustomer(
            $validated['order_number'],
            $validated['phone'] ?? null,
            $validated['email'] ?? null,
        );

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'No order found for that order number and contact details.',
            ], 422);
        }

        $this->tracking->markVerified($order);

        return response()->json([
            'success' => true,
            'message' => 'Order found. Opening tracking details.',
            'redirect_url' => route('order.track', $order->order_number),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('customer')->logout();

        return response()->json([
            'success' => true,
            'message' => 'You have been signed out.',
        ]);
    }

    private function customerPayload(Customer $customer): array
    {
        return [
            'name' => $customer->name,
            'email' => $customer->email,
        ];
    }
}
