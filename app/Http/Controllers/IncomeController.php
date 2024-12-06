<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::where('company_id',Auth::user()->company_id)->get();

        return Inertia::render('Incomes/Index', [
            'incomes' => $incomes,
        ]);
    }

    public function create()
    {
        $companies = Company::where('id',Auth::user()->company_id)->get();

        return Inertia::render('Incomes/Create', [
            'companies' => $companies,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'tax_base' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'date' => 'required|date',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['tax_amount'] = ($validated['tax_base'] * $validated['tax_rate']) / 100;
        $validated['total_amount'] = $validated['tax_base'] + $validated['tax_amount'];

        Income::create($validated);

        return Inertia::location(route('incomes.index'));
    }

    public function show(Income $income)
    {
        return Inertia::render('Incomes/Show', [
            'income' => $income->load('company'),
        ]);
    }

    public function edit(Income $income)
    {
        $companies = Company::where('id',Auth::user()->company_id)->get();

        return Inertia::render('Incomes/Edit', [
            'income' => $income,
            'companies' => $companies,
        ]);
    }

    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'tax_base' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'date' => 'required|date',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['tax_amount'] = ($validated['tax_base'] * $validated['tax_rate']) / 100;
        $validated['total_amount'] = $validated['tax_base'] + $validated['tax_amount'];

        $income->update($validated);

        return Inertia::location(route('incomes.index'));
    }

    public function destroy(Income $income)
    {
        $income->delete();

    return Inertia::location(route('incomes.index'));
    }
}

