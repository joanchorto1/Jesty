<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public function build()
    {
        Log::info('Enviando email con:', [
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'user' => $this->notification->user->email,
            'company' => $this->notification->user->company->email,
        ]);

        return $this->from(
            $this->notification->user->company->email ?? 'no-reply@jesty.com',
            $this->notification->user->company->name ?? 'Holii'
        )
            ->subject($this->notification->title ?? 'Notificación')
            ->html("
                <h1 style='color: dodgerblue;'>" . e($this->notification->title) . "</h1>
                <p>" . e($this->notification->message) . "</p>
            ");
    }




}
