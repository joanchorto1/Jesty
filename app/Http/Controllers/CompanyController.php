<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\RoleFeature;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        return Inertia::render('Companies/Index', [
            'companies' => $companies
        ]);
    }

    public function create()
    {
        return Inertia::render('Companies/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'nif' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'plan_id' => 'required',
        ]);

        Company::create($request->all());

        return Inertia::location(route('admin.dashboard'));
    }

    public function edit(Company $company)
    {
        return Inertia::render('Companies/Edit', [
            'company' => $company
        ]);
    }

    public function update(Request $request, Company $company)
    {

        $plan_id = $company->plan_id;
        $request->validate([
            'name' => 'required',
            'nif' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
        ]);


        $request['plan_id'] = $plan_id;

        $company->update($request->all());

return Inertia::location(route('dashboard.admin'));
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index');
    }

    public function changePlan(Company $company)
    {
        return Inertia::render('Companies/ChangePlan', [
            'company' => $company,
            'plans' => Plan::all(),
            'plan' => Plan::where('id', $company->plan_id)->first(),
            'features' => Feature::all(),
            'planFeatures' => PlanFeature::all()

        ]);
    }
    public function updatePlan(Request $request, Company $company)
    {

        $request->validate([
        'plan_id' => 'required',
        ]);


        // Actualizar el plan de la compañía
        $company->update(['plan_id' => $request->input('plan_id')]);

        // Obtener todos los roles de la compañía excepto el de "Administrador"
        $roles = \App\Models\Role::where('company_id', $company->id)->get();
        $adminRole = $roles->where('name', 'Administrador')->first();

        if ($adminRole) {
            $roles = $roles->except($adminRole->id);
        }

        // Eliminar roles no permitidos por el nuevo plan
        foreach ($roles as $role) {
            $role->delete();
        }

        // Obtener las características del nuevo plan
        $newPlan = Plan::with('features')->findOrFail($company->plan_id);
        $newPlanFeatures = $newPlan->features->pluck('id')->toArray();

        // Actualizar las características del rol "Administrador"
        if ($adminRole) {
            // Obtener características actuales del rol
            $currentRoleFeatures = RoleFeature::where('role_id', $adminRole->id)->pluck('feature_id')->toArray();

            // Identificar características a agregar y eliminar
            $featuresToAdd = array_diff($newPlanFeatures, $currentRoleFeatures);
            $featuresToRemove = array_diff($currentRoleFeatures, $newPlanFeatures);

            // Agregar nuevas características
            foreach ($featuresToAdd as $featureId) {
                RoleFeature::create([
                    'role_id' => $adminRole->id,
                    'feature_id' => $featureId,
                ]);
            }

            // Eliminar características que sobran
            RoleFeature::where('role_id', $adminRole->id)
                ->whereIn('feature_id', $featuresToRemove)
                ->delete();
        }

        return Inertia::location(route('dashboard.admin'));
    }
}
