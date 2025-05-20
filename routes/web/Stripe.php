<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Stripe\Webhook;

Route::post('/stripe/webhook', [\App\Http\Controllers\AuthController::class, 'handle']);

Route::post('/checkout', [\App\Http\Controllers\AuthController::class, 'createCheckoutSession'])->name('checkout.session');

