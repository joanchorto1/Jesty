<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\EmailConfiguration;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf; // Importa DomPDF
use phpseclib3\Crypt\RSA;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

// Importa la clase RSA


class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('company_id', auth()->user()->company_id)->get();
        $clients = Client::where('company_id', auth()->user()->company_id)->get();
        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
            'clients' => $clients
        ]);
    }

    public function create()
    {
        $clients = Client::where('company_id', Auth::user()->company_id)->get();
        $companies = Company::all();
        $products = Product::where('company_id', Auth::user()->company_id)->get();
        return Inertia::render('Invoices/Create', [
            'clients' => $clients,
            'companies' => $companies,
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'client_id' => 'required',
                'date' => 'required|date',
                'base_imponible' => 'required|numeric|min:0',
                'iva' => 'nullable|numeric',
                'name' => 'required|string|max:255',
                'state' => 'required|string|in:pagado,no_pagado',
            ]);

            $data = $request->all();
            $data['company_id'] = Auth::user()->company_id;

            Invoice::create($data);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function storeWithItems(Request $request)
    {

        $validated = $request->validate([
            'date' => 'required|date',
            'name' => 'required|string',
            'base_imponible' => 'required|numeric',
            'state' => 'required|string',
            'client_id' => 'required|exists:clients,id',
            'total' => 'required|numeric',
            'iva' => 'required|numeric',
            'monto_iva' => 'required|numeric',
            'invoiceItems' => 'required|array',
            'invoiceItems.*.product_id' => 'required|exists:products,id',
            'invoiceItems.*.quantity' => 'required|integer',
            'invoiceItems.*.discount' => 'required|numeric',
            'invoiceItems.*.unit_price' => 'required|numeric',
            'invoiceItems.*.total' => 'required|numeric',
        ]);

        $data = $request->only('date', 'name', 'base_imponible', 'state', 'client_id', 'total', 'iva', 'monto_iva');
        $data['company_id'] = Auth::user()->company_id;
        $invoice = Invoice::create($data);

        foreach ($validated['invoiceItems'] as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'],
                'total' => $item['total'],
            ]);
            $this->updateStockProduct($item['product_id'], $item['quantity']);
        }
        if ($invoice->state == 'paid') {
            $this->createIncomeFromInvoice($invoice);
        }

        return Inertia::location(route('invoices.index'));
    }

    public function edit(Invoice $invoice)
    {
        $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();
        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice,
            'invoiceItems' => $invoiceItems,
            'products' => Product::where('company_id', Auth::user()->company_id)->get(),
            'clients' => Client::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'required',
            'date' => 'required|date',
            'base_imponible' => 'required|numeric|min:0',
            'iva' => 'nullable|numeric',
            'total' => 'required|numeric',
            'monto_iva' => 'required|numeric',
            'name' => 'required|string|max:255',
            'state' => 'required|string|in:paid,cancelled,pending',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::findOrFail($id);

        $data = $request->only(['client_id', 'date', 'base_imponible', 'iva', 'name', 'state', 'total', 'monto_iva']);
        $invoice->update($data);

        $oldItems = InvoiceItem::where('invoice_id', $id)->get();
        InvoiceItem::where('invoice_id', $id)->delete();

        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'discount' => $item['discount'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ]);
            $this->updateStrockProductFromOldItems($item['product_id'], $item['quantity'],$oldItems);

            if ($invoice->state == 'cancelled') {
                $this->restoreStockProduct($item['product_id'], $item['quantity']);
            }
        }

        $this->updateIncomeFromInvoice($invoice);

        return Inertia::location(route('invoices.index'));
    }

    public function destroy(Invoice $invoice)
    {
        $this->destroyIncomeFromInvoice($invoice);

        InvoiceItem::where('invoice_id', $invoice->id)->delete();
        $invoice->delete();

        return Inertia::location(route('invoices.index'));
    }

    public function show(Invoice $invoice)
    {
        $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
            'invoiceItems' => $invoiceItems,
            'products' => Product::where('company_id', Auth::user()->company_id)->get(),
            'clients' => Client::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }

    public function print(Invoice $invoice)
    {
        $client = Client::find($invoice->client_id);
        $company = Company::find(Auth::user()->company_id);
        $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();
        $products = Product::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Invoices/Print', [
            'invoice' => $invoice,
            'invoiceItems' => $invoiceItems,
            'client' => $client,
            'company' => $company,
            'products' => $products
        ]);
    }
    public function createFromBudget(Budget $budget)
    {
        try {

            $budget['state'] = 'accepted';
            $budget->save();
            // Crear la factura con los datos del presupuesto
            $invoice = Invoice::create([
                'company_id' => $budget->company_id,
                'client_id' => $budget->client_id,
                'date' => now(), // O puedes usar $budget->date si quieres mantener la misma fecha
                'total' => $budget->total,
                'state' => 'pending',
                'monto_iva' => $budget->monto_iva,
                'base_imponible' => $budget->base_imponible,
                'iva' => $budget->iva,
                'name' => $budget->name,
            ]);

            // Copiar los ítems del presupuesto a los ítems de la factura
            $budgetItems = BudgetItem::where('budget_id', $budget->id)->get();
            foreach ($budgetItems as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ]);
            }
            $InvoiceItem = InvoiceItem::where('invoice_id', $invoice->id)->get();

            //Comprobar si se ha creado la factura
            // Redirigir a la vista de la factura creada
            return Inertia::location(route('invoices.show', ['invoice' => $invoice->id]));
        } catch (Exception $e) {
            // Manejo de errores
            return redirect()->back()->with('error', 'Hubo un problema al crear la factura.');
        }
    }

    public function copy(Invoice $invoice)
    {
        //crear nueva factura a partir de la que recivimos
        $newInvoice= Invoice::create([
                'name'=>$invoice->name."(copy)",
                'client_id'=>$invoice->client_id,
                'date'=>$invoice->date,
                'base_imponible'=>$invoice->base_imponible,
                'iva'=>$invoice->iva,
                'total'=>$invoice->total,
                'state'=>$invoice->state,
                'company_id'=>$invoice->company_id,
                'monto_iva'=>$invoice->monto_iva
            ]);

        //copiar los items de la factura a la nueva factura
        $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();
        foreach ($invoiceItems as $item) {
            InvoiceItem::create([
                'invoice_id' => $newInvoice->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ]);

        }

        if ($newInvoice->state == 'paid') {
            $this->createIncomeFromInvoice($newInvoice);
        }
        return Inertia::location(route('invoices.index'));
    }

    private function createIncomeFromInvoice(Invoice $invoice)
    {
        Income::create(
            ['source' => 'invoice',
                'name' => $invoice->name,
                'company_id' => $invoice->company_id,
                'tax_base' => $invoice->base_imponible,
                'tax_rate' => $invoice->iva,
                'external_id'=> $invoice->id,
                'tax_amount' => $invoice->monto_iva,
                'total_amount' => $invoice->total,
                'date' => $invoice->date,
            ]
        );
    }
    private function updateIncomeFromInvoice(Invoice $invoice)
    {
        Income::where('source','invoice')->where('external_id',$invoice->id)->where('company_id',Auth::user()->company_id)->delete();
        Income::create(
            ['source' => 'invoice',
                'name' => $invoice->name,
                'company_id' => $invoice->company_id,
                'tax_base' => $invoice->base_imponible,
                'tax_rate' => $invoice->iva,
                'external_id'=> $invoice->id,
                'tax_amount' => $invoice->monto_iva,
                'total_amount' => $invoice->total,
                'date' => $invoice->date,
            ]
        );
    }

    private function destroyIncomeFromInvoice(Invoice $invoice)
    {
        Income::where('source', 'invoice')->where('external_id', $invoice->id)->where('company_id',Auth::user()->company_id)->delete();
    }

    private function updateStockProduct($product_id, $quantity)
    {
        $product = Product::find($product_id);
        $product->stock = $product->stock - $quantity;
        $product->save();

    }
    private function updateStrockProductFromOldItems($product_id, $quantity,$oldItems)
    {
        foreach ($oldItems as $item) {
            $product = Product::find($item->product_id);
            $product->stock = $product->stock + $item->quantity;
            $product->save();
        }


        $product = Product::find($product_id);
        $product->stock = $product->stock - $quantity;
        $product->save();
    }

    private function restoreStockProduct($product_id, $quantity)
    {
        $product = Product::find($product_id);
        $product->stock = $product->stock + $quantity;
        $product->save();
    }


    public function send($invoiceId)
    {
        // Obtén los datos de la factura, la empresa y el cliente
        $invoice = Invoice::findOrFail($invoiceId);

        // Personaliza los detalles del correo
        $fromEmail = Auth::user()->email; // Email del remitente (empresa)
        $client = Client::where('id', $invoice->client_id)->first();
        $toEmail =$client->email;  // Email del destinatario (cliente)
        $company=Company::find(Auth::user()->company_id);
        $fromName= $company->name;


        $companyEmailConfig = EmailConfiguration::where('company_id', $company->id)->first();

        // Cambiar la configuración de correo de manera dinámica
        Config::set('mail.mailers.smtp.host', $companyEmailConfig->smtp_host);
        Config::set('mail.mailers.smtp.port', $companyEmailConfig->smtp_port);
        Config::set('mail.mailers.smtp.username', $companyEmailConfig->smtp_username);
        Config::set('mail.mailers.smtp.password', $companyEmailConfig->smtp_password);
        Config::set('mail.mailers.smtp.encryption', $companyEmailConfig->smtp_encryption);;
        // Enviar el correo
        Mail::to($toEmail)
            ->send(new InvoiceMail($invoice, $fromEmail, $fromName));



    }

    public function generateInvoicePdf($invoiceId)
    {
        // Obtén los datos de la factura
        $invoice = Invoice::with('items', 'client', 'company')->findOrFail($invoiceId);

        // Genera el PDF usando una vista Blade
        $pdf = Pdf::loadView('pdfs.invoice', [
            'invoice' => $invoice,
            'client' => $invoice->client,
            'company' => $invoice->company
        ]);

        // Guardar temporalmente el PDF (opcional)
        $filePath = storage_path("app/public/invoice-{$invoice->id}.pdf");
        $pdf->save($filePath);

        // Aplicar la firma digital
        $company = Company::findOrFail(Auth::user()->company_id); // Obtener la empresa autenticada
        $privateKey = unserialize(Crypt::decryptString($company->private_key));
        // Leer el archivo PDF generado
        $pdfContent = file_get_contents($filePath);

        // Firmar el contenido del PDF
        $signedPdf = $this->applyDigitalSignature($pdfContent, $privateKey);

        // Guardar el PDF firmado
        $signedFilePath = storage_path("app/public/invoice-{$invoice->id}-signed.pdf");
        file_put_contents($signedFilePath, $signedPdf);



        // Devuelve la ruta del archivo firmado
        return $signedFilePath;
    }



    private function applyDigitalSignature($pdfContent, $privateKey)
    {
        // Inicializar la clase RSA con la clave privada
        $rsa = RSA::load($privateKey);

        // Firmar el contenido del PDF (en formato binario)
        $signature = $rsa->sign($pdfContent);

        // Agregar la firma al PDF (usaremos una sección del PDF donde añadirla o simplemente agregarla al final)
        $signedPdf = $this->addSignatureToPdf($pdfContent, $signature);

        return $signedPdf;
    }

    private function addSignatureToPdf($pdfContent, $signature)
    {
        // Crear una instancia de FPDI
        $pdf = new FPDI();

        // Usar el contenido binario del PDF
        $pdf->setSourceFile(StreamReader::createByString($pdfContent));

        // Importar la primera página del PDF (para usarla en el archivo original)
        $templateId = $pdf->importPage(1);
        $pdf->addPage();
        $pdf->useTemplate($templateId);



        // Si el documento tiene más de una página, importa la segunda página
        if ($pdf->setSourceFile(StreamReader::createByString($pdfContent)) > 1) {
            // Importar la segunda página
            $templateId2 = $pdf->importPage(2);
            $pdf->addPage();
            $pdf->useTemplate($templateId2);

            // Ajustar la posición del texto al final de la segunda página
            $pdf->SetFont('Helvetica', '', 12);
            $pdf->SetXY(10, 100); // Ajustar la posición a un área cerca del final de la página

            // Agregar el texto de la firma
            $signatureText = "Firma Digital: " . base64_encode($signature);
            $pdf->Write(0, $signatureText);
        }else{
            // Ajustar la posición del texto al final de la segunda página
            $pdf->SetFont('Helvetica', '', 12);
            $pdf->SetXY(10, 200); // Ajustar la posición a un área cerca del final de la página

            // Agregar el texto de la firma
            $signatureText = "Firma Digital: " . base64_encode($signature);
            $pdf->Write(0, $signatureText);
        }

        // Generar el PDF con la firma añadida y devolverlo
        return $pdf->Output('S');
    }




}
