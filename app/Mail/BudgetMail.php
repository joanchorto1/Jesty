<?php

namespace App\Mail;

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;


class BudgetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $budget;

    protected $fromEmail;
    protected $fromName;

    public function __construct($budget, $fromEmail, $fromName)
    {
        $this->budget = $budget;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }


    public function build()
    {
        // Genera el PDF
        $filePath = (new BudgetController())->generateBudgetPdf($this->budget->id);

        return $this->from($this->budget->company->email, $this->budget->company->name)
            ->subject('Presupuesto #' . $this->budget->id)
            ->view('emails.budget') // La vista HTML del correo
            ->with([
                'budget' => $this->budget,
                'client' => $this->budget->client, // Datos del cliente
                'company' => $this->budget->company, // Datos de la empresa
            ])
            ->attach($filePath, [
                'as' => "Presupuesto_{$this->budget->id}.pdf",
                'mime' => 'application/pdf',
            ]);
    }
}
