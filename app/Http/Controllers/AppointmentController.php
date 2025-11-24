<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        $appointments = Appointment::with('user')
            ->where('company_id', $user->company_id)
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->get();

        $users = User::where('company_id', $user->company_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'users' => $users,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'user_id' => ['required', 'exists:users,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $conflict = Appointment::where('company_id', $user->company_id)
            ->where('scheduled_date', $data['scheduled_date'])
            ->where('scheduled_time', $data['scheduled_time'])
            ->exists();

        if ($conflict) {
            return redirect()
                ->back()
                ->withErrors(['scheduled_time' => 'Ja hi ha una cita registrada en aquesta franja.'])
                ->withInput();
        }

        Appointment::create([
            'company_id' => $user->company_id,
            'user_id' => $data['user_id'],
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'],
            'guest_name' => $data['guest_name'],
            'subject' => $data['subject'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Cita afegida correctament.');
    }
}
