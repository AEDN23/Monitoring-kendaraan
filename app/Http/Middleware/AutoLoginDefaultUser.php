<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginDefaultUser
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $userData = Cache::remember('cached_default_user_attrs', 86400, function () {
                $u = User::first();
                if (! $u) {
                    $u = User::create([
                        'name'     => 'Pemilik Kendaraan',
                        'email'    => 'owner@monitoring.local',
                        'password' => bcrypt('password'),
                    ]);
                }
                return $u ? $u->getAttributes() : null;
            });

            if ($userData) {
                $user = (new User)->newFromBuilder($userData);
                Auth::setUser($user);
            }
        } catch (\Throwable $e) {
            // Ignore if DB connection is not established
        }

        return $next($request);
    }
}
