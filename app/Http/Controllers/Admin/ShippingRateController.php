<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingRateController extends Controller
{
    public function index(): View
    {
        return view('admin.shipping.rates.index', [
            'rates' => ShippingRate::query()->with('zone')->orderBy('shipping_zone_id')->orderBy('method_name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.shipping.rates.form', [
            'rate' => new ShippingRate(['status' => 'active', 'method_name' => 'standard_delivery']),
            'zones' => ShippingZone::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        ShippingRate::query()->create($this->validated($request));

        return redirect()->route('admin.shipping.rates.index')->with('success', 'Shipping rate created.');
    }

    public function edit(ShippingRate $rate): View
    {
        return view('admin.shipping.rates.form', [
            'rate' => $rate,
            'zones' => ShippingZone::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ShippingRate $rate): RedirectResponse
    {
        $rate->update($this->validated($request));

        return redirect()->route('admin.shipping.rates.index')->with('success', 'Shipping rate updated.');
    }

    public function destroy(ShippingRate $rate): RedirectResponse
    {
        $rate->delete();

        return redirect()->route('admin.shipping.rates.index')->with('success', 'Shipping rate deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'shipping_zone_id' => ['required', 'exists:shipping_zones,id'],
            'method_name' => ['required', 'string', 'max:100'],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'min_weight' => ['nullable', 'numeric', 'min:0'],
            'max_weight' => ['nullable', 'numeric', 'min:0'],
            'extra_rate' => ['nullable', 'numeric', 'min:0'],
            'free_shipping_min_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
