<?php

namespace App\Http\Controllers;

use App\Models\RecurringExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RecurringExpenseController extends Controller
{
    public function updateStatus(Request $request, RecurringExpense $recurringExpense)
    {
        $this->authorizeForCompany($recurringExpense);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'paused'])],
            'next_run_at' => ['nullable', 'date'],
        ]);

        $data = ['status' => $validated['status']];

        if ($request->filled('next_run_at')) {
            $data['next_run_at'] = Carbon::parse($validated['next_run_at']);
        }

        if ($validated['status'] === 'paused') {
            $data['next_run_at'] = $recurringExpense->next_run_at;
        }

        $recurringExpense->update($data);

        return back()->with('status', 'Recurring expense updated');
    }

    public function destroy(RecurringExpense $recurringExpense)
    {
        $this->authorizeForCompany($recurringExpense);

        $recurringExpense->update([
            'status' => 'inactive',
            'next_run_at' => null,
            'ends_at' => now(),
        ]);

        return back()->with('status', 'Recurring expense archived');
    }

    protected function authorizeForCompany(RecurringExpense $recurringExpense): void
    {
        if ($recurringExpense->company_id !== Auth::user()?->company_id) {
            abort(403);
        }
    }
}
