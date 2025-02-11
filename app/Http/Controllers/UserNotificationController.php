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

    }

    // Eliminar una notificación
    public function destroy(UserNotification $userNotification)
    {
        $userNotification->delete();

        return response()->json(['message' => 'Notificación eliminada con éxito.']);
    }

    //funcion general para crear notificaciones
    public static function createNotification($user_id, $title, $message)
    {
        $notification = new UserNotification();
        $notification->user_id = $user_id;
        $notification->title = $title;
        $notification->message = $message;
        $notification->read = false;
        $notification->save();

        //fer el push de la notificación

        //que la notificación se envíe
    }
}
