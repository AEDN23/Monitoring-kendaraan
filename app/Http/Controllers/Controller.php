<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Ambil daftar kendaraan user.
     */
    protected function cachedUserVehicles(bool $activeOnly = false, bool $withOdometer = false): \Illuminate\Database\Eloquent\Collection
    {
        $userId = Auth::id();
        $q = Vehicle::where('user_id', $userId)->orderBy('name');

        if ($activeOnly) {
            $q->where('is_active', true);
        }

        if ($withOdometer) {
            $q->withLatestOdometer();
        }

        return $q->get();
    }

    /**
     * Clear cache kendaraan user (helper tetap ada untuk backward compatibility).
     */
    protected function clearVehicleCache(): void
    {
        // No-op karena langsung query ke database lokal (cepat < 1ms)
    }
}
