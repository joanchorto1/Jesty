<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EmailConfiguration;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class AuthController extends Controller
{
    public function create()
    {

        Log::info('Iniciando registro de usuario');
        // Verificar si el usuario ya está autenticado
      Log::info('Planes disponibles', ['plans' => Plan::all()]);
      Log::info('Características disponibles', ['features' => Feature::all()]);
      Log::info('Características de los planes', ['planFeatures' => PlanFeature::all()]);



        return Inertia::render('Auth/Register', [
            'plans' => Plan::all(),
            'features' => Feature::all(),
            'planFeatures' => PlanFeature::all(),
        ]);
    }

    public function createCheckoutSession(Request $request)
    {
        Log::info('Iniciando creación de sesión de checkout');

        Log::info('Stripe API Key', ['key' => env('STRIPE_SECRET')]);
        Log::info('Stripe API Key', ['key' => config('services.stripe.secret')]);

//        Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe::setApiKey(config('services.stripe.secret'));



        $plan = Plan::findOrFail($request->plan_id);

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $plan->stripe_price_id,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => url('/login'),
                'cancel_url' => url('/cancel'),
                'metadata' => $request->except('_token'),
            ]);

            Log::info('Sesión de checkout creada correctamente', ['session_id' => $session->id]);

            return response()->json(['sessionId' => $session->id]);

        } catch (\Exception $e) {
            Log::error('Error al crear la sesión de checkout', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::error('Webhook Stripe inválido', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $data = $session->metadata;

            if (!$data || !isset($data->email)) {
                Log::error('Webhook incompleto: metadata faltante', ['session_id' => $session->id]);
                return response()->json(['error' => 'Metadata incompleta'], 400);
            }

            Log::info('Webhook recibido: checkout.session.completed', ['email' => $data->email]);

            if (User::where('email', $data->email)->exists()) {
                Log::warning('Usuario ya existe en webhook', ['email' => $data->email]);
                return response()->json(['status' => 'User already exists']);
            }

            $company = Company::create([
                'name' => $data->company_name ?? '',
                'address' => $data->company_address ?? '',
                'phone' => $data->company_phone ?? '',
                'email' => $data->company_email ?? '',
                'nif' => $data->company_nif ?? '',
                'plan_id' => $data->plan_id ?? null,
                'stripe_customer_id' => $session->customer,
                'stripe_subscription_id' => $session->subscription,
            ]);

            User::create([
                'name' => $data->name ?? '',
                'email' => $data->email,
                'password' => bcrypt($data->password ?? 'defaultpassword'),
                'company_id' => $company->id,
            ]);

            EmailConfiguration::create([
                'company_id' => $company->id,
                'driver' => 'smtp',
                'host' => 'smtp.mailtrap.io',
                'port' => '2525',
                'username' => 'your-username',
                'password' => 'your-password',
                'encryption' => 'tls',
                'from_address' => 'your-email',
                'from_name' => 'your-name',
            ]);

            Log::info('Usuario y compañía creados desde webhook', ['email' => $data->email]);
        }

        return response()->json(['status' => 'success']);
    }
}
