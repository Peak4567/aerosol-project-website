<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceMode = Setting::get('maintenance_mode', '0');

        if ($maintenanceMode === '1') {
            $user = Auth::user();
            $isAdmin = $user && $user->isAdmin();

            if (!$isAdmin) {
                // Allow maintenance page, login, logout, and essential API routes
                if ($request->is('maintenance') ||
                    $request->is('login') ||
                    $request->is('logout') ||
                    $request->is('api/health') ||
                    $request->is('api/sensor-data')) {
                    return $next($request);
                }

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['message' => 'Service Unavailable (Maintenance Mode)'], 503);
                }

                return redirect()->route('maintenance');
            }
        }

        return $next($request);
    }
}

