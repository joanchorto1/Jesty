<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\RoleFeature;

class RouteFeaturesAccess
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $featureId
     * @return mixed
     */
    public function handle(Request $request, Closure $next, int $featureId)
    {
        $user = Auth::user();

        // Verifica si el usuario tiene acceso a la funcionalidad
        $hasAccess = RoleFeature::where('role_id', $user->role_id)
            ->where('feature_id', $featureId)
            ->exists();

        if ($hasAccess) {
            return $next($request);
        }

        // Redirige si no tiene acceso
        return Inertia::location(route('dashboard'))->with('error', 'No tienes permiso para acceder a esta funcionalidad.');
    }
}

