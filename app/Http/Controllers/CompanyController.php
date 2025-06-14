<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\RoleFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;
use phpseclib3\Crypt\RSA;

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
//            Passar todos los planes excepto el que tenga name = FirstMonthFree
            'plans' => Plan::where('name', '!=', 'FirstMonthFree')->get(),

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

    public function updateKeys(Request $request, Company $company)
    {
        $request->validate([
            'public_key' => 'required',
            'private_key' => 'required',
        ]);

        // Encriptar las claves
        $encryptedData = [
            'public_key' => Crypt::encrypt($request->input('public_key')),
            'private_key' => Crypt::encrypt($request->input('private_key')),
        ];

        $company->update($encryptedData);

        return Inertia::location(route('dashboard.admin'));
    }


    public function showKeys(Company $company)
    {

        if (!$company->public_key || !$company->private_key) {
            return Inertia::location(route('companies.generateKeys', $company));
        }else{
            // Deserialitzar i desxifrar la clau pública i privada
            $publicKey = unserialize(Crypt::decryptString($company->public_key));
            $privateKey = unserialize(Crypt::decryptString($company->private_key));

            return Inertia::render('Companies/ShowKeys', [
                'company' => $company,
                'public_key' => $publicKey,
                'private_key' => $privateKey
            ]);
        }

    }


    public function generateKeys(Company $company)
    {
        // Generate new RSA keys
        $rsa = RSA::createKey(2048);

        // Update the company's keys
        $publicKey = $rsa->getPublicKey()->toString('PKCS8');
         $privateKey= $rsa->toString('PKCS1'); // Optional: Store securely if needed

        $encryptedData = [
            'public_key' => Crypt::encrypt($publicKey),
            'private_key' => Crypt::encrypt($privateKey),
        ];

        $company->update($encryptedData);

        return Inertia::location(route('companies.showKeys', $company));
    }


}
