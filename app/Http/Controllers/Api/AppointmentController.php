<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function availability(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date'],
        ]);

        $user = $request->user();

        $appointments = Appointment::where('company_id', $user->company_id)
            ->where('scheduled_date', $request->string('date'))
            ->get(['scheduled_time']);

        $busyTimes = $appointments
            ->pluck('scheduled_time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'))
            ->all();

        $start = Carbon::parse($request->date . ' 09:00');
        $end = Carbon::parse($request->date . ' 17:00');

        $available = [];
        for ($slot = $start->copy(); $slot <= $end; $slot->addHour()) {
            $formatted = $slot->format('H:i');
            if (!in_array($formatted, $busyTimes, true)) {
                $available[] = $formatted;
            }
        }

        return response()->json([
            'date' => $request->date,
            'available' => $available,
            'busy' => $busyTimes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'user_id' => ['required', 'exists:users,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $exists = Appointment::where('company_id', $user->company_id)
            ->where('scheduled_date', $data['scheduled_date'])
            ->where('scheduled_time', $data['scheduled_time'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ja existeix una cita en aquesta data i hora.',
            ], 409);
        }

        $appointment = Appointment::create([
            'company_id' => $user->company_id,
            'user_id' => $data['user_id'],
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'],
            'guest_name' => $data['guest_name'],
            'subject' => $data['subject'],
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json($appointment, 201);
    }
}
