<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use App\Models\EmailConfiguration;
use App\Models\Feature;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;



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

            $companyEmailConfig = EmailConfiguration::where('company_id', $company->id)->first();

            // Cambiar la configuración de correo de manera dinámica
            Config::set('mail.mailers.smtp.host', $companyEmailConfig->smtp_host);
            Config::set('mail.mailers.smtp.port', $companyEmailConfig->smtp_port);
            Config::set('mail.mailers.smtp.username', $companyEmailConfig->smtp_username);
            Config::set('mail.mailers.smtp.password', $companyEmailConfig->smtp_password);
            Config::set('mail.mailers.smtp.encryption', $companyEmailConfig->smtp_encryption);
            //enviar la notificación
           $this->sendNotification($notification);
        }
    }


    //funcion para enviar notificaciones

    public function sendNotification(UserNotification $notification)
    {
        $user = User::find($notification->user_id);
        Log::info('Enviant notificació a: '.$user->email);

        try {

            Mail::to($user->email)->send(new NotificationMail($notification));
        } catch (\Exception $e) {
            Log::error('Error al enviar el email: '.$e->getMessage());
        }

    }



}
