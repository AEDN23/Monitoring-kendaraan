<?php

namespace App\Http\Controllers;

use App\Models\FuelTransaction;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil kendaraan aktif dari middleware EnsureActiveVehicle
        $activeVehicle = $request->get('activeVehicle');

        // Semua kendaraan aktif milik user (untuk switcher jika ingin ganti)
        $allVehicles = $this->cachedUserVehicles(activeOnly: true, withOdometer: true);

        if (! $activeVehicle && $allVehicles->isNotEmpty()) {
            $activeVehicle = $allVehicles->first();
        }

        $now = now();
        $year = $now->year;
        $month = $now->month;

        // Statistik khusus kendaraan yang sedang aktif
        $activeVehicleId = $activeVehicle ? $activeVehicle->id : null;

        $totalFuelThisMonth = $activeVehicleId
            ? FuelTransaction::where('vehicle_id', $activeVehicleId)
                ->whereYear('transaction_at', $year)
                ->whereMonth('transaction_at', $month)
                ->sum('total_amount')
            : 0;

        $totalServiceThisMonth = $activeVehicleId
            ? MaintenanceRecord::where('vehicle_id', $activeVehicleId)
                ->whereYear('service_at', $year)
                ->whereMonth('service_at', $month)
                ->sum('total_cost')
            : 0;

        // Jadwal perawatan kendaraan aktif
        $schedules = collect();
        $worstStatus = 'safe';

        if ($activeVehicle) {
            $odometer = $activeVehicle->latestOdometerKm() ?? 0;
            $rawSchedules = $activeVehicle->maintenanceSchedules()
                ->where('is_active', true)
                ->with('maintenanceItem')
                ->get();

            $statusPriority = ['overdue' => 3, 'soon' => 2, 'needs_inspection' => 1, 'safe' => 0];

            $schedules = $rawSchedules->map(function ($s) use ($odometer) {
                $status = $s->status($odometer);
                return [
                    'schedule'    => $s,
                    'name'        => $s->maintenanceItem->name ?? '-',
                    'status'      => $status,
                    'next_due_km' => $s->nextDueKm(),
                    'next_date'   => $s->nextDueDate(),
                ];
            });

            foreach ($schedules as $sItem) {
                if (($statusPriority[$sItem['status']] ?? 0) > ($statusPriority[$worstStatus] ?? 0)) {
                    $worstStatus = $sItem['status'];
                }
            }
        }

        // Transaksi BBM & Servis terakhir kendaraan aktif
        $recentFuels = $activeVehicleId
            ? FuelTransaction::where('vehicle_id', $activeVehicleId)
                ->with('fuelType')
                ->orderBy('transaction_at', 'desc')
                ->limit(5)
                ->get()
            : collect();

        $recentServices = $activeVehicleId
            ? MaintenanceRecord::where('vehicle_id', $activeVehicleId)
                ->with('items')
                ->orderBy('service_at', 'desc')
                ->limit(5)
                ->get()
            : collect();

        return view('dashboard', compact(
            'activeVehicle',
            'allVehicles',
            'totalFuelThisMonth',
            'totalServiceThisMonth',
            'worstStatus',
            'schedules',
            'recentFuels',
            'recentServices'
        ));
    }
}
