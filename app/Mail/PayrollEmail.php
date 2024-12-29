<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayrollEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $payroll;
    protected $fromEmail;
    protected $fromName;
    protected $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct($payroll, $fromEmail, $fromName, $pdfPath)
    {
        $this->payroll = $payroll;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from($this->fromEmail, $this->fromName)
            ->subject('Payroll Details for ' . $this->payroll->id)
            ->view('emails.payroll') // La vista HTML del correo
            ->with([
                'payroll' => $this->payroll,
                'employee' => $this->payroll->employee, // Datos del empleado
            ])
            ->attach($this->pdfPath, [
                'as' => "Payroll_{$this->payroll->id}.pdf",
                'mime' => 'application/pdf',
            ]);
    }
}
