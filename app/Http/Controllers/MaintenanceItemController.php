<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceItem;
use Illuminate\Http\Request;

class MaintenanceItemController extends Controller
{
    public function index()
    {
        $items = MaintenanceItem::orderBy('name')->paginate(15);

        return view('maintenance-items.index', compact('items'));
    }

    public function create()
    {
        return view('maintenance-items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:120',
            'category'              => 'nullable|string|max:80',
            'default_interval_km'   => 'nullable|integer|min:100',
            'default_interval_days' => 'nullable|integer|min:1',
            'tracking_mode'         => 'required|in:km,time,km_or_time,condition',
            'applies_to'            => 'required|in:motorcycle,car,both,other',
            'description'           => 'nullable|string',
            'is_active'             => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        MaintenanceItem::create($validated);

        \Illuminate\Support\Facades\Cache::forget('maintenance_items_active');

        return redirect()->route('maintenance-items.index')
            ->with('success', 'Komponen perawatan berhasil ditambahkan.');
    }

    public function edit(MaintenanceItem $maintenanceItem)
    {
        return view('maintenance-items.edit', compact('maintenanceItem'));
    }

    public function update(Request $request, MaintenanceItem $maintenanceItem)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:120',
            'category'              => 'nullable|string|max:80',
            'default_interval_km'   => 'nullable|integer|min:100',
            'default_interval_days' => 'nullable|integer|min:1',
            'tracking_mode'         => 'required|in:km,time,km_or_time,condition',
            'applies_to'            => 'required|in:motorcycle,car,both,other',
            'description'           => 'nullable|string',
            'is_active'             => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $maintenanceItem->update($validated);

        \Illuminate\Support\Facades\Cache::forget('maintenance_items_active');

        return redirect()->route('maintenance-items.index')
            ->with('success', 'Komponen perawatan berhasil diperbarui.');
    }

    /**
     * Toggle is_active instead of deleting (item may be used in relations).
     */
    public function destroy(MaintenanceItem $maintenanceItem)
    {
        $maintenanceItem->update(['is_active' => ! $maintenanceItem->is_active]);
        \Illuminate\Support\Facades\Cache::forget('maintenance_items_active');

        $status = $maintenanceItem->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('maintenance-items.index')
            ->with('success', "Komponen perawatan berhasil {$status}.");
    }
}
