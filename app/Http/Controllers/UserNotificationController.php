<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;



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
    public function createNotification($title, $message, $type)
    {

        //Separar els id dels usuaris depenent del seu rol i del les funcionalitats de cada rol.La funcionalitat la rebem pel type.

        Log::info('Type: '.$type);
        Log::info('Message: '.$message);
        Log::info('Title: '.$title);


        $feature = Feature::where('name', $type)->first();

        Log::info('Feature: '.$feature);

        $roles = $feature->roles;

        Log::info('Roles: '.$roles);

        $users = $roles->map(function ($role) {
            return $role->users;
        })->flatten();

        Log::info('Users: '.$users);

        $company = Auth::user()->company;

        $users = $users->filter(function ($user) use ($company) {
            return $user->company_id == $company->id;
        });

        //crear la notificación
        foreach ($users as $user) {
            $notification = UserNotification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'read' => false,
                'type' => $type
            ]);

            Log::info('Notificación creada para usuario: '.$user->id);
        }
    }


    // La función se mantiene para compatibilidad pero ya no envía correos.
    public function sendNotification(UserNotification $notification)
    {
        Log::info('Notificación lista para el dashboard: '.$notification->id);
    }



}
