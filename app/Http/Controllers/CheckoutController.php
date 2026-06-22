<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payments\PaymentManager;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OrderService $orders,
        private readonly PaymentManager $payments,
        private readonly ShippingService $shipping,
    ) {}

    public function show(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty. Add products before checkout.');
        }

        $cart = $this->cart->summary();
        $shippingQuote = $this->buildQuote(
            old('country', 'Pakistan'),
            old('province', ''),
            old('city', '')
        );

        return view('pages.checkout', [
            'cart' => $cart,
            'checkoutTotals' => $this->initialCheckoutTotals($shippingQuote, old('shipping_method')),
            'shippingQuote' => $shippingQuote,
            'paymentMethods' => $this->payments->availableForCheckout(),
        ]);
    }

    public function shippingQuote(Request $request): JsonResponse
    {
        if ($this->cart->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 422);
        }

        $validated = $request->validate([
            'country' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
        ]);

        $quote = $this->buildQuote(
            $validated['country'],
            $validated['province'] ?? '',
            $validated['city']
        );

        $discount = $this->cart->discount();
        $subtotal = $this->cart->subtotal();

        return response()->json([
            ...$quote,
            'totals' => [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping' => $quote['methods'][0]['amount'] ?? 0,
                'total' => max(0, $subtotal + ($quote['methods'][0]['amount'] ?? 0) - $discount),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $paymentMethod = $request->string('payment_method')->toString();

        if ($paymentMethod === 'cod') {
            $paymentMethod = 'cash_on_delivery';
        }

        $rules = [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'in:Pakistan,India,United Arab Emirates,Saudi Arabia,United Kingdom,United States'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_method' => ['required', 'string', 'in:standard_delivery,express_delivery,free_shipping'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cash_on_delivery,jazzcash,cod'],
        ];

        if ($paymentMethod === 'jazzcash') {
            $rules['payment_account_number'] = ['required', 'string', 'max:20'];
            $rules['payment_account_name'] = ['required', 'string', 'max:120'];
            $rules['payment_reference'] = ['required', 'string', 'max:100'];
        }

        $validated = $request->validate($rules);
        $validated['payment_method'] = $paymentMethod;
        $validated['payment_reference'] = $this->resolvePaymentReference($validated);
        $validated['payment_notes'] = $this->buildPaymentNotes($validated);

        $shippingMethod = $this->shipping->resolveMethod([
            'country' => $validated['country'],
            'province' => $validated['province'],
            'city' => $validated['city'],
            'subtotal' => $this->cart->subtotal(),
            'discount' => $this->cart->discount(),
            'weight' => $this->cart->totalWeight(),
        ], $validated['shipping_method']);

        if (! $shippingMethod) {
            return back()
                ->withInput()
                ->with('error', 'Shipping is not available for this location. Please contact support.');
        }

        $quote = $this->buildQuote(
            $validated['country'],
            $validated['province'],
            $validated['city']
        );

        $validated['shipping_amount'] = (int) $shippingMethod['amount'];
        $validated['shipping_zone'] = $quote['zone'];
        $validated['total_weight'] = $this->cart->totalWeight();

        try {
            $result = $this->orders->place($validated);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('order.success', $result['order']->order_number)
            ->with('success', $result['message']);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildQuote(string $country, string $province, string $city): array
    {
        return $this->shipping->quote([
            'country' => $country,
            'province' => $province,
            'city' => $city,
            'subtotal' => $this->cart->subtotal(),
            'discount' => $this->cart->discount(),
            'weight' => $this->cart->totalWeight(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $shippingQuote
     * @return array<string, int|float>
     */
    private function initialCheckoutTotals(array $shippingQuote, ?string $selectedMethod): array
    {
        $methods = $shippingQuote['methods'] ?? [];
        $selected = collect($methods)->firstWhere('key', $selectedMethod) ?? $methods[0] ?? null;
        $shippingAmount = (int) ($selected['amount'] ?? 0);

        return $this->cart->checkoutTotals($shippingAmount);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolvePaymentReference(array $validated): ?string
    {
        return match ($validated['payment_method']) {
            'jazzcash' => $validated['payment_reference'] ?? null,
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function buildPaymentNotes(array $validated): ?string
    {
        return match ($validated['payment_method']) {
            'jazzcash' => $this->formatNotes([
                'JazzCash Mobile' => $validated['payment_account_number'] ?? null,
                'Account Holder' => $validated['payment_account_name'] ?? null,
            ]),
            default => null,
        };
    }

    /**
     * @param  array<string, string|null>  $lines
     */
    private function formatNotes(array $lines): ?string
    {
        $formatted = collect($lines)
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value, $label) => "{$label}: {$value}")
            ->implode("\n");

        return $formatted !== '' ? $formatted : null;
    }
}
