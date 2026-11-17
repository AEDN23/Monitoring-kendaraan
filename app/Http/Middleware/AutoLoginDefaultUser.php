<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginDefaultUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            try {
                $user = User::first();

                if (! $user) {
                    $user = User::create([
                        'name'     => 'Pemilik Kendaraan',
                        'email'    => 'owner@monitoring.local',
                        'password' => bcrypt('password'),
                    ]);
                }

                Auth::login($user);
            } catch (\Throwable $e) {
                // Ignore if DB connection is not established during certain console commands
            }
        }

        return $next($request);
    }
}
