<?php

namespace App\Http\Controllers;

use App\Models\FuelType;
use Illuminate\Http\Request;

class FuelTypeController extends Controller
{
    public function index()
    {
        $fuelTypes = FuelType::orderBy('name')->paginate(15);

        return view('fuel-types.index', compact('fuelTypes'));
    }

    public function create()
    {
        return view('fuel-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:80|unique:fuel_types,name',
            'is_active' => 'boolean',
        ]);

        FuelType::create([
            'name'      => $request->name,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('fuel-types.index')
            ->with('success', 'Jenis BBM berhasil ditambahkan.');
    }

    public function edit(FuelType $fuelType)
    {
        return view('fuel-types.edit', compact('fuelType'));
    }

    public function update(Request $request, FuelType $fuelType)
    {
        $request->validate([
            'name'      => 'required|string|max:80|unique:fuel_types,name,' . $fuelType->id,
            'is_active' => 'boolean',
        ]);

        $fuelType->update([
            'name'      => $request->name,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('fuel-types.index')
            ->with('success', 'Jenis BBM berhasil diperbarui.');
    }

    /**
     * Toggle is_active instead of deleting (no deleted_at column).
     */
    public function destroy(FuelType $fuelType)
    {
        $fuelType->update(['is_active' => ! $fuelType->is_active]);

        $status = $fuelType->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('fuel-types.index')
            ->with('success', "Jenis BBM berhasil {$status}.");
    }
}
