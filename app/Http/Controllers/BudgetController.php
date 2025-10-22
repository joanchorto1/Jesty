<?php

namespace App\Http\Controllers;

use App\Mail\BudgetMail;
use App\Mail\InvoiceMail;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Category;
use App\Models\Client;
use App\Models\Company;
use App\Models\EmailConfiguration;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\DocumentTotalsCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use App\Services\DocumentNumberGenerator;
use Illuminate\Validation\Rule;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::where('company_id', auth()->user()->company_id)->get();
        $clients = Client::where('company_id', auth()->user()->company_id)->get();
        return Inertia::render('Budgets/Index', [
            'budgets' => $budgets,
            'clients' => $clients
        ]);
    }

    public function create()
    {
        $clients = Client::where('company_id', Auth::user()->company_id)->get();
        $companies = Company::all();
        $products = Product::where('company_id', Auth::user()->company_id)
            ->where('disabled', false)
            ->with('category')
            ->get();

        $nextBudgetNumber = DocumentNumberGenerator::generate(
            Budget::class,
            'name',
            'PR',
            Auth::user()->company_id,
            Carbon::now()
        );

        return Inertia::render('Budgets/Create', [
            'clients' => $clients,
            'companies' => $companies,
            'products' => $products,
            'categories' => Category::where('company_id', Auth::user()->company_id)->get(),
            'nextBudgetNumber' => $nextBudgetNumber,
        ]);


    }


    //store function el company_id lo sacamos del modelo Auth::user()->company_id
    public function store(Request $request)
    {
        try {
            $request->validate([
                'client_id' => 'required',
                'date' => 'required|date',
                'base_imponible' => 'required|numeric|min:0',
                'iva' => 'nullable|numeric',
                'state' => 'required|string|in:draft,approved,rejected',
            ]);

            $issueDate = Carbon::parse($request->input('date'));
            $data = $request->except('name');
            $data['company_id'] = Auth::user()->company_id;
            $data['name'] = DocumentNumberGenerator::generate(
                Budget::class,
                'name',
                'PR',
                Auth::user()->company_id,
                $issueDate
            );

            Budget::create($data);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }



    }

    public function storeWithItems(Request $request)
    {
        // Validar los datos del request
        $validated = $request->validate([
            'date' => 'required|date',
            'state' => 'required|string',
            'client_id' => 'required|exists:clients,id',
            'budgetItems' => 'required|array',
            'budgetItems.*.product_id' => 'required|exists:products,id',
            'budgetItems.*.quantity' => 'required|integer',
            'budgetItems.*.discount' => 'required|numeric',
            'budgetItems.*.unit_price' => 'required|numeric',
            'budgetItems.*.iva' => 'nullable|numeric',
        ]);

        // Crear el presupuesto
        $issueDate = Carbon::parse($validated['date']);
        $totals = DocumentTotalsCalculator::calculate($validated['budgetItems']);

        $data = [
            'date' => $validated['date'],
            'state' => $validated['state'],
            'client_id' => $validated['client_id'],
            'base_imponible' => $totals['base'],
            'monto_iva' => $totals['tax'],
            'total' => $totals['total'],
            'iva' => $totals['effectiveRate'],
        ];
        $data['company_id']=Auth::user()->company_id;
        $data['name'] = DocumentNumberGenerator::generate(
            Budget::class,
            'name',
            'PR',
            Auth::user()->company_id,
            $issueDate
        );
        $budget = Budget::create($data);

        // Crear los ítems de presupuesto
        foreach ($validated['budgetItems'] as $item) {
            $lineBase = $item['quantity'] * $item['unit_price'];
            if ($item['discount'] > 0) {
                $lineBase -= ($lineBase * $item['discount']) / 100;
            }
            $lineBase = round($lineBase, 2);

            BudgetItem::create([
                'budget_id' => $budget->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'],
                'total' => $lineBase,
                'iva' => $item['iva'] ?? 0,
            ]);
        }


        $notificationController = new UserNotificationController();
        $notificationController->createNotification('Nuevo presupuesto', 'Se ha creado un nuevo presupuesto', 'Facturación');
        // Devolver respuesta adecuada
        return Inertia::location(route('budgets.index'));
    }


    public function edit(Budget $budget)
    {
        $budgetItems = BudgetItem::where('budget_id', $budget->id)->get();


        return Inertia::render('Budgets/Edit', [
            'budget' => $budget,
            'budgetItems' => $budgetItems,
            'products' => Product::where('company_id', Auth::user()->company_id)
                ->with('category')
                ->get(),
            'clients' => Client::where('company_id', Auth::user()->company_id)->get(),
            'categories' => Category::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'client_id' => 'required',
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'state' => 'required|string|in:in_process,accepted,rejected',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.iva' => 'nullable|numeric',
        ]);

        $budget = Budget::findOrFail($id);

        $totals = DocumentTotalsCalculator::calculate($validated['items']);

        // Actualizar el presupuesto
        $budget->update([
            'client_id' => $validated['client_id'],
            'date' => $validated['date'],
            'name' => $validated['name'],
            'state' => $validated['state'],
            'base_imponible' => $totals['base'],
            'monto_iva' => $totals['tax'],
            'total' => $totals['total'],
            'iva' => $totals['effectiveRate'],
        ]);

        // Eliminar ítems existentes
        BudgetItem::where('budget_id', $id)->delete();

        // Crear nuevos ítems
        foreach ($validated['items'] as $item) {
            $lineBase = $item['quantity'] * $item['unit_price'];
            if ($item['discount'] > 0) {
                $lineBase -= ($lineBase * $item['discount']) / 100;
            }
            $lineBase = round($lineBase, 2);

            BudgetItem::create([
                'budget_id' => $id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'],
                'total' => $lineBase,
                'iva' => $item['iva'] ?? 0,
            ]);
        }

        if ($budget->state == 'accepted') {
            app(UserNotificationController::class)->createNotification('Presupuesto aceptado ID: '.$budget->id, 'Se ha aceptado un presupuesto.', 'Facturación');
        } elseif ($budget->state == 'rejected') {
            app(UserNotificationController::class)->createNotification('Presupuesto rechazado ID: '.$budget->id, 'Se ha rechazado un presupuesto.', 'Facturación');
        }

        return Inertia::location(route('budgets.index'));
    }

    public function updateStatus(Request $request, Budget $budget)
    {
        if ($budget->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'state' => ['required', Rule::in(['accepted', 'in_process', 'rejected'])],
        ]);

        $previousState = $budget->state;

        if ($previousState !== $validated['state']) {
            $budget->forceFill(['state' => $validated['state']])->save();

            if ($budget->state === 'accepted') {
                app(UserNotificationController::class)->createNotification(
                    'Presupuesto aceptado ID: ' . $budget->id,
                    'Se ha aceptado un presupuesto.',
                    'Facturación'
                );
            } elseif ($budget->state === 'rejected') {
                app(UserNotificationController::class)->createNotification(
                    'Presupuesto rechazado ID: ' . $budget->id,
                    'Se ha rechazado un presupuesto.',
                    'Facturación'
                );
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Estado del presupuesto actualizado correctamente.',
                'budget' => $budget->fresh(),
            ]);
        }

        return back()->with('success', 'Estado del presupuesto actualizado correctamente.');
    }

    public function destroy(Budget $budget)
    {
        // Eliminar los ítems relacionados
        BudgetItem::where('budget_id', $budget->id)->delete();


        app(UserNotificationController::class)->createNotification('Presupuesto eliminado ID: '.$budget->id, 'Se ha eliminado un presupuesto.', 'Facturación');

        // Eliminar el presupuesto
        $budget->delete();


        return Inertia::location(route('budgets.index'));
    }



    public function show(Budget $budget)
    {
        // Obtener los ítems relacionados con el presupuesto usando el modelo BudgetItem
        $budgetItems = BudgetItem::where('budget_id', $budget->id)->get();

        return Inertia::render('Budgets/Show', [
            'budget' => $budget,
            'budgetItems' => $budgetItems,
            'products' => Product::where('company_id', Auth::user()->company_id)->get(),
            'clients' => Client::where('company_id', Auth::user()->company_id)->get(),

        ]);
    }
    public function print(Budget $budget)
    {

        $client = Client::find($budget->client_id);
        $company = Company::find(Auth::user()->company_id);// Suponiendo que tienes un solo registro de la compañía.
        $budgetItems = BudgetItem::where('budget_id',$budget->id)->get();
        $products = Product::where('company_id', Auth::user()->company_id)->get();
        return Inertia::render('Budgets/Print', [
            'budget' => $budget,
            'budgetItems' => $budgetItems,
            'client' => $client,
            'company' => $company,
            'products' => $products
        ]);
    }

    public function send($budgetId)
    {
        // Obtén los datos de la factura, la empresa y el cliente
        $budget = Budget::findOrFail($budgetId);

        // Personaliza los detalles del correo
        $fromEmail = Auth::user()->email; // Email del remitente (empresa)
        $client = Client::where('id', $budget->client_id)->first();
        $toEmail =$client->email;  // Email del destinatario (cliente)
        $company=Company::find(Auth::user()->company_id);
        $fromName= $company->name;



        $companyEmailConfig = EmailConfiguration::where('company_id', $company->id)->first();

        // Cambiar la configuración de correo de manera dinámica
        Config::set('mail.mailers.smtp.host', $companyEmailConfig->smtp_host);
        Config::set('mail.mailers.smtp.port', $companyEmailConfig->smtp_port);
        Config::set('mail.mailers.smtp.username', $companyEmailConfig->smtp_username);
        Config::set('mail.mailers.smtp.password', $companyEmailConfig->smtp_password);
        Config::set('mail.mailers.smtp.encryption', $companyEmailConfig->smtp_encryption);

        app(UserNotificationController::class)->createNotification('Presupuesto enviado ID: '.$budget->id, 'Se ha enviado un presupuesto.', 'Facturación');
        // Enviar el correo
        Mail::to($toEmail)
            ->send(new BudgetMail($budget, $fromEmail, $fromName));


    }

    public function generateBudgetPdf($budgetId)
    {
        // Obtén los datos del budget
        $budget = Budget::with('items', 'client', 'company')->findOrFail($budgetId);

        // Genera el PDF usando una vista Blade
        $pdf = Pdf::loadView('pdfs.budget', ['budget' => $budget, 'client' => $budget->client, 'company' => $budget->company]);

        // Guarda temporalmente el PDF (opcional)
        $filePath = storage_path("app/public/budget-{$budget->id}.pdf");
        $pdf->save($filePath);

//        return $filePath; // Devuelve la ruta del archivo generado
        return $filePath;
    }


}
