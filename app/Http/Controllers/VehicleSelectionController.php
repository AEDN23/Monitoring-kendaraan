<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleSelectionController extends Controller
{
    /**
     * Tampilkan halaman pilih kendaraan (portal masuk).
     */
    public function index()
    {
        $vehicles = Auth::user()
            ->vehicles()
            ->where('is_active', true)
            ->withLatestOdometer()
            ->orderBy('name')
            ->get();

        // Jika belum ada kendaraan sama sekali, arahkan ke pembuatan kendaraan pertama
        if ($vehicles->isEmpty()) {
            return redirect()->route('vehicles.create')
                ->with('info', 'Silakan daftarkan kendaraan pertama Anda terlebih dahulu.');
        }

        $activeVehicleId = session('active_vehicle_id');

        return view('vehicles.select', compact('vehicles', 'activeVehicleId'));
    }

    /**
     * Set kendaraan yang dipilih ke dalam session.
     */
    public function select(Vehicle $vehicle)
    {
        // Pastikan kendaraan milik user yang sedang aktif
        if ($vehicle->user_id !== Auth::id() || ! $vehicle->is_active) {
            abort(403);
        }

        session(['active_vehicle_id' => $vehicle->id]);

        return redirect()->route('dashboard')
            ->with('success', "Kendaraan aktif diubah ke {$vehicle->name}.");
    }
}
