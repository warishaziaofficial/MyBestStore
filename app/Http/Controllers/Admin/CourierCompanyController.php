<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourierCompanyController extends Controller
{
    public function index(): View
    {
        return view('admin.couriers.index', [
            'couriers' => CourierCompany::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.couriers.form', [
            'courier' => new CourierCompany(['status' => 'active']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        CourierCompany::query()->create($this->validated($request));

        return redirect()->route('admin.couriers.index')->with('success', 'Courier company created.');
    }

    public function edit(CourierCompany $courier): View
    {
        return view('admin.couriers.form', [
            'courier' => $courier,
        ]);
    }

    public function update(Request $request, CourierCompany $courier): RedirectResponse
    {
        $courier->update($this->validated($request));

        return redirect()->route('admin.couriers.index')->with('success', 'Courier company updated.');
    }

    public function destroy(CourierCompany $courier): RedirectResponse
    {
        $courier->delete();

        return redirect()->route('admin.couriers.index')->with('success', 'Courier company deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'tracking_url' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
