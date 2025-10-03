<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::all();
        return Inertia::render('PaymentMethods/Index', [
            'paymentMethods' => $paymentMethods
        ]);
    }

    public function create()
    {
        return Inertia::render('PaymentMethods/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $validated['company_id'] = Auth::user()->company_id;

        PaymentMethod::create($validated);

        return Inertia::location(route('paymentMethods.index'));
    }

    public function show(PaymentMethod $paymentMethod)
    {
        $paymentMethod->load('company');

        return Inertia::render('PaymentMethods/Show', [
            'paymentMethod' => $paymentMethod
        ]);
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return Inertia::render('PaymentMethods/Edit', [
            'method' => $paymentMethod
        ]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['company_id'] = Auth::user()->company_id;

        $paymentMethod->update($validated);

        return Inertia::location(route('paymentMethods.index'));
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return Inertia::location(route('paymentMethods.index'));
    }
}
