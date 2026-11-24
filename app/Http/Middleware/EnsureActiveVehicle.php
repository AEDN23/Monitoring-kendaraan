<?php

namespace App\Http\Middleware;

use App\Models\Vehicle;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveVehicle
{
    /**
     * Rute-rute yang dikecualikan dari pengecekan kendaraan aktif.
     */
    protected array $exceptRoutes = [
        'vehicle.select',
        'vehicle.select.post',
        'vehicles.create',
        'vehicles.store',
        'login',
        'register',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $currentRoute = $request->route() ? $request->route()->getName() : null;

        // Skip jika mengakses rute yang dikecualikan
        if (in_array($currentRoute, $this->exceptRoutes)) {
            return $next($request);
        }

        $activeVehicleId = session('active_vehicle_id');
        $activeVehicle = null;

        if ($activeVehicleId && Auth::check()) {
            $activeVehicle = Vehicle::where('id', $activeVehicleId)
                ->where('user_id', Auth::id())
                ->where('is_active', true)
                ->withLatestOdometer()
                ->first();
        }

        // Jika belum ada kendaraan aktif atau kendaraan tidak valid
        if (! $activeVehicle) {
            session()->forget('active_vehicle_id');
            return redirect()->route('vehicle.select');
        }

        // Bagikan $activeVehicle ke seluruh view Blade
        View::share('activeVehicle', $activeVehicle);

        // Tempelkan ke request agar controller mudah mengambilnya
        $request->attributes->set('activeVehicle', $activeVehicle);

        return $next($request);
    }
}
