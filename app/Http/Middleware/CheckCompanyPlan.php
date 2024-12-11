<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckCompanyPlan
{

    public function handle(Request $request, Closure $next)
    {

        if (Auth::check()) {
            $user = Auth::user();
            // Obtén la compañía asociada al usuario.
            $company = Company::where('id', $user->company_id)->first();

            // Verifica si la compañía existe.
            if (!$company) {
                return redirect('/error')->with('message', 'No se ha encontrado una compañía asociada.');
            }

            // Verifica si el plan es FirstMonthFree.

            $firstMonthFree = Plan::where('name', 'FirstMonthFree')->first();
            if ($company->plan_id === $firstMonthFree->id) {
                $createdAt = $company->created_at;
                $daysSinceCreation = now()->diffInDays($createdAt);

                // Si han pasado más de 30 días, verifica el rol.
                if ($daysSinceCreation > 30) {
                    $role = Role::where('name', 'Administrador')->first();
                    if ($user->role_id === $role->id) {
                        return Inertia::location(route('company.changePlan', $company));
                    } else {
                        Auth::logout();
                        return redirect('/login')->with('error', 'Tu compañía necesita actualizar su plan para continuar.');
                    }
                }
            }

        }

        return $next($request);

    }
}
