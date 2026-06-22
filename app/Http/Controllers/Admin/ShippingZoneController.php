<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingZoneController extends Controller
{
    public function index(): View
    {
        return view('admin.shipping.zones.index', [
            'zones' => ShippingZone::query()->withCount('rates')->orderBy('country')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.shipping.zones.form', [
            'zone' => new ShippingZone(['status' => 'active', 'is_remote' => false, 'country' => 'Pakistan']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        ShippingZone::query()->create($validated);

        return redirect()->route('admin.shipping.zones.index')->with('success', 'Shipping zone created.');
    }

    public function edit(ShippingZone $zone): View
    {
        return view('admin.shipping.zones.form', [
            'zone' => $zone,
        ]);
    }

    public function update(Request $request, ShippingZone $zone): RedirectResponse
    {
        $zone->update($this->validated($request));

        return redirect()->route('admin.shipping.zones.index')->with('success', 'Shipping zone updated.');
    }

    public function destroy(ShippingZone $zone): RedirectResponse
    {
        $zone->delete();

        return redirect()->route('admin.shipping.zones.index')->with('success', 'Shipping zone deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'is_remote' => ['nullable', 'boolean'],
            'status' => ['required', 'in:active,inactive'],
        ]) + ['is_remote' => $request->boolean('is_remote')];
    }
}
