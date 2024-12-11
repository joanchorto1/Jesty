<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;


class UserNotificationController extends Controller
{
    // Obtener notificaciones de un usuario
    public function index(Request $request)
    {
        $notifications = UserNotification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    // Marcar una notificación como leída
    public function markAsRead(UserNotification $userNotification)
    {
        $userNotification->update(['read' => true]);

        return response()->json(['message' => 'Notificación marcada como leída.']);
    }

    // Eliminar una notificación
    public function destroy(UserNotification $userNotification)
    {
        $userNotification->delete();

        return response()->json(['message' => 'Notificación eliminada con éxito.']);
    }
}
