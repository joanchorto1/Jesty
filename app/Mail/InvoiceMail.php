<?php

    namespace App\Mail;

    use App\Http\Controllers\InvoiceController;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Mail\Mailable;
    use Illuminate\Mail\Mailables\Content;
    use Illuminate\Mail\Mailables\Envelope;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Support\Facades\Storage;


    class InvoiceMail extends Mailable
    {
        use Queueable, SerializesModels;

        public $invoice;

        protected $fromEmail;
        protected $fromName;

        public function __construct($invoice, $fromEmail, $fromName)
        {
            $this->invoice = $invoice;
            $this->fromEmail = $fromEmail;
            $this->fromName = $fromName;
        }


        public function build()
        {
            // Genera el PDF
            $filePath = (new InvoiceController())->generateInvoicePdf($this->invoice->id);

            return $this->from($this->invoice->company->email, $this->invoice->company->name)
                ->subject('Factura #' . $this->invoice->id)
                ->view('emails.invoice') // La vista HTML del correo
                ->with([
                    'invoice' => $this->invoice,
                    'client' => $this->invoice->client, // Datos del cliente
                    'company' => $this->invoice->company, // Datos de la empresa
                ])
                ->attach($filePath, [
                    'as' => "Factura_{$this->invoice->id}.pdf",
                    'mime' => 'application/pdf',
                ]);
        }
    }
