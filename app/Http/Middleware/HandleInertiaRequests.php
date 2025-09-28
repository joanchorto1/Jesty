<?php

namespace App\Http\Middleware;

use App\Models\Feature;
use App\Models\RoleFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Middleware;


class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {

        $features = [];

        if (Auth::check()) {
            // Obtener los features del rol del usuario autenticado
            $roleFeatures = RoleFeature::where('role_id', Auth::user()->role_id)->get();
            $features = Feature::whereIn('id', $roleFeatures->pluck('feature_id'))
                ->where('is_active', true)
                ->get();

            // Convertir la colección a un array
            $features = $features->toArray();
        }

        return array_merge(parent::share($request), [
            // Compartir los features con todas las vistas
            'auth.user' => fn () => $request->user() ? $request->user()->only('id', 'name', 'email') : null,
            'features' => $features, // Aquí se agrega
        ]);
    }
}
