<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Milon\Barcode\DNS1D;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('company_id',Auth::user()->company_id)->get();
        $products = Product::where('company_id',Auth::user()->company_id)->where('disabled', false)->get();
        $categories = Category::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    public function create()
    {
        $companies = Company::all();
        $categories = Category::where('company_id',Auth::user()->company_id)->get();
        $suppliers = Supplier::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('Products/Create', [
            'companies' => $companies,
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'supplier_id' => 'required',
            'cost_price' => 'required|numeric',
            'stock' => 'required_if:is_stackable,true',
            'is_stackable' => 'required'
        ]);

        $data = $request->all();
        $data['company_id'] = Auth::user()->company_id;

        if (!$request->is_stackable) {
            $data['stock'] = 0;
        }

        // Generar código de barras único
        $data['codebar'] = Str::random(12);

        // Guardar producto
        $product = Product::create($data);

        // Generar y guardar la etiqueta del producto
        $labelPath = $this->generateProductLabel($product);
        $product->update(['label_path' => $labelPath]);

        return Inertia::location(route('products.index'));
    }
    public function edit(Product $product)
    {
        $companies = Company::all();
        $suppliers = Supplier::where('company_id',Auth::user()->company_id)->get();
        $categories = Category::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('Products/Edit', [
            'product' => $product,
            'companies' => $companies,
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'stock' => 'required_if:is_stackable,true',
            'cost_price' => 'required',
            'supplier_id' => 'required',
            'is_stackable' => 'required'
        ]);

        // Actualizar los datos del producto
        $product->update($request->all());

        // Volver a generar el PDF con la nueva información
        $this->generateProductLabel($product);

        return Inertia::location(route('products.index'));
    }



    public function generateProductLabel($product)
    {
        $barcodePath = 'barcodes/product_' . $product->id . '.png';

        // Si el código de barras no existe, generarlo
        if (!Storage::disk('public')->exists($barcodePath)) {
            $barcodeGenerator = new DNS1D();
            $barcodeData = $barcodeGenerator->getBarcodePNG($product->codebar, 'C128', 2, 50);
            Storage::disk('public')->put($barcodePath, base64_decode($barcodeData));
        }

        // Generar el PDF con la información actualizada del producto
        $pdf = Pdf::loadView('pdfs.product_label', [
            'product' => $product,
            'barcodePath' => $barcodePath
        ]);

        // Guardar el PDF sobrescribiendo el anterior
        $pdfPath = 'labels/product_' . $product->id . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());

        return $pdfPath;
    }


    //show

    public function show()
    {

    }

    public function destroy(Product $product)
    {


        $product->update(['disabled' => true]);
        return Inertia::location(route('products.index'));
    }


}
