<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\Role;
use App\Models\RoleFeature;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        return Inertia::render('Auth/Register', [
            'plans' => Plan::all(),
            'features' => Feature::all(),
            'planFeatures' => PlanFeature::all()

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $request->validate([
            // Datos de la compañía
            'company_name' => 'required',
            'company_address' => 'required',
            'company_phone' => 'required',
            'company_nif' => 'required',
            'company_email' => 'required|email',

            // Plan
            'plan_id' => 'required',

            // Datos del usuario
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'address' => 'required',
            'phone' => 'required',

            // Pago
            'payment_method' => 'required', // Recibimos el token de pago
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        try {
            // Configurar Stripe
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Crear Intento de Pago
            $paymentIntent = PaymentIntent::create([
                'amount' => $plan->price * 100, // Convertir a centavos
                'currency' => 'usd',
                'payment_method' => $request->payment_method,
                'confirm' => true,
            ]);

            if ($paymentIntent->status === 'succeeded') {
                // Crear compañía
                $company = Company::create([
                    'name' => $request->company_name,
                    'address' => $request->company_address,
                    'phone' => $request->company_phone,
                    'email' => $request->company_email,
                    'nif' => $request->company_nif,
                    'plan_id' => $request->plan_id,
                ]);

                // Crear rol y características
                $role = Role::create([
                    'name' => 'Administrador',
                    'description' => 'Administrador de la empresa',
                    'company_id' => $company->id,
                ]);

                $planFeatures = PlanFeature::where('plan_id', $request->plan_id)->get();
                foreach ($planFeatures as $feature) {
                    RoleFeature::create([
                        'role_id' => $role->id,
                        'feature_id' => $feature->feature_id,
                    ]);
                }

                // Crear usuario
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'company_id' => $company->id,
                    'role_id' => $role->id,
                ]);

                return redirect()->route('login')->with('success', 'Registro exitoso y pago procesado.');
            }

            return back()->withErrors('El pago no pudo ser procesado.');
        } catch (\Exception $e) {
            return back()->withErrors('Error al procesar el pago: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
