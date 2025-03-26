<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EmailConfiguration;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\Role;
use App\Models\RoleFeature;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;


class AuthController extends Controller
{

    public function create()
    {
        return Inertia::render('Auth/Register', [
            'plans' => Plan::all(),
            'features' => Feature::all(),
            'planFeatures' => PlanFeature::all()

        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required',
            'company_address' => 'required',
            'company_phone' => 'required',
            'company_nif' => 'required',
            'company_email' => 'required|email',
            'plan_id' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'address' => 'required',
            'phone' => 'required',
            'payment_method' => 'required',
        ]);

        $stripeService = new StripeService();

        try {
            // Crear el cliente en Stripe
            $customer = $stripeService->createCustomer($request);

            // Crear suscripción
            $plan = Plan::findOrFail($request->plan_id);
            $subscription = $stripeService->createSubscription($customer->id, $plan->stripe_price_id, $request->payment_method);

            // Guardar la compañía y usuario
            $company = Company::create([
                'name' => $request->company_name,
                'address' => $request->company_address,
                'phone' => $request->company_phone,
                'email' => $request->company_email,
                'nif' => $request->company_nif,
                'plan_id' => $request->plan_id,
                'stripe_customer_id' => $customer->id,
                'stripe_subscription_id' => $subscription->id,
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'company_id' => $company->id,
            ]);

            return redirect()->route('login')->with('success', 'Registro y suscripción completados.');

        } catch (\Exception $e) {
            return back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        Log::info('Creating checkout session');

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Suscripción Premium',
                        ],
                        'unit_amount' => 1000,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => url('/success'),
                'cancel_url' => url('/cancel'),
            ]);
        }catch (\Exception $e) {
            return back()->withErrors('Error: ' . $e->getMessage());
        }


        return response()->json(['sessionId' => $session->id]);
    }


    }
