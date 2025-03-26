<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Stripe\Webhook;

Route::post('/stripe/webhook', function (Request $request) {
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $secret = env('STRIPE_WEBHOOK_SECRET');

    try {
        $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        // Maneja los eventos aquí
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }

    return response()->json(['status' => 'success']);
});

Route::post('/checkout', [\App\Http\Controllers\AuthController::class, 'createCheckoutSession'])->name('checkout.session');

