<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\ProductRelationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly ProductRelationService $relations,
    ) {}

    public function index()
    {
        $cart = $this->cart->summary();
        $cartSlugs = array_column($cart['items'] ?? [], 'slug');
        $crossSellProducts = $cartSlugs !== []
            ? $this->relations->forCart($cartSlugs, 4)
            : [];

        return view('pages.cart', [
            'cart' => $cart,
            'crossSellProducts' => $crossSellProducts,
        ]);
    }

    public function drawer()
    {
        return view('components.cart-drawer-panel', [
            'cart' => $this->cart->summary(),
        ]);
    }

    public function add(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'redirect' => ['nullable', 'string'],
        ]);

        if (! $this->cart->add($validated['slug'], (int) ($validated['quantity'] ?? 1))) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product could not be added to cart.',
                ], 422);
            }

            return back()->with('error', 'Product could not be added to cart.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart.',
                'count' => $this->cart->count(),
            ]);
        }

        if (($validated['redirect'] ?? null) === 'checkout') {
            return redirect()->route('checkout')->with('success', 'Product added. Continue checkout.');
        }

        if (($validated['redirect'] ?? null) === 'cart') {
            return redirect()->route('cart')->with('success', 'Product added to cart.');
        }

        if ($request->boolean('open_cart')) {
            return back()->with('success', 'Product added to cart.')->with('open_cart', true);
        }

        return back()->with('success', 'Product added to cart.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'coupon' => ['required', 'string', 'max:50'],
            'redirect' => ['nullable', 'string', 'in:cart,checkout'],
        ]);

        $error = $this->cart->applyCoupon($validated['coupon']);

        if ($error !== null) {
            $route = ($validated['redirect'] ?? 'cart') === 'checkout' ? 'checkout' : 'cart';

            return redirect()->route($route)->with('error', $error);
        }

        $route = ($validated['redirect'] ?? 'cart') === 'checkout' ? 'checkout' : 'cart';

        return redirect()->route($route)->with('success', 'Discount code applied.');
    }

    public function removeCoupon(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'redirect' => ['nullable', 'string', 'in:cart,checkout'],
        ]);

        $this->cart->removeCoupon();

        $route = ($validated['redirect'] ?? 'cart') === 'checkout' ? 'checkout' : 'cart';

        return redirect()->route($route)->with('success', 'Discount code removed.');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update($validated['slug'], (int) $validated['quantity']);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string'],
        ]);

        $this->cart->remove($validated['slug']);

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return back()->with('success', 'Cart cleared.');
    }
}
