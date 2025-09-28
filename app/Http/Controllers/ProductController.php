<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Milon\Barcode\DNS1D;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('company_id',Auth::user()->company_id)->where('disabled', false)->get();
        $categories = Category::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $categories = Category::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('Products/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'periodicity' => 'nullable|string',
            'supplier_id' => 'required_if:is_stackable,true|nullable|exists:suppliers,id',
            'cost_price' => 'required_if:is_stackable,true|nullable|numeric',
            'stock' => 'required_if:is_stackable,true|nullable|integer',
            'is_stackable' => 'sometimes|boolean'
        ]);

        $data = $request->only([
            'name',
            'category_id',
            'description',
            'price',
            'periodicity',
            'supplier_id',
            'cost_price',
            'stock',
            'is_stackable',
        ]);

        $data['company_id'] = Auth::user()->company_id;
        $data['is_stackable'] = $request->boolean('is_stackable', false);

        if (! $data['is_stackable']) {
            $data['stock'] = null;
            $data['supplier_id'] = null;
            $data['cost_price'] = null;
            $data['codebar'] = null;
            $data['label_path'] = null;
        } else {
            $data['codebar'] = $data['codebar'] ?? Str::random(12);
        }

        $product = Product::create($data);

        if ($product->is_stackable) {
            $labelPath = $this->generateProductLabel($product);
            $product->update(['label_path' => $labelPath]);
        }

        return Inertia::location(route('products.index'));
    }
    public function edit(Product $product)
    {
        $categories = Category::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric',
            'periodicity' => 'nullable|string',
            'stock' => 'required_if:is_stackable,true|nullable|integer',
            'cost_price' => 'required_if:is_stackable,true|nullable|numeric',
            'supplier_id' => 'required_if:is_stackable,true|nullable|exists:suppliers,id',
            'is_stackable' => 'sometimes|boolean'
        ]);

        $data = $request->only([
            'name',
            'description',
            'category_id',
            'price',
            'periodicity',
            'stock',
            'cost_price',
            'supplier_id',
            'is_stackable',
        ]);

        $data['is_stackable'] = $request->boolean('is_stackable', false);

        if (! $data['is_stackable']) {
            $data['stock'] = null;
            $data['supplier_id'] = null;
            $data['cost_price'] = null;
            $data['codebar'] = null;
            $data['label_path'] = null;
        } else {
            $data['codebar'] = $product->codebar ?? Str::random(12);
        }

        $product->update($data);

        if ($product->is_stackable) {
            $labelPath = $this->generateProductLabel($product);
            $product->update(['label_path' => $labelPath]);
        }

        return Inertia::location(route('products.index'));
    }
    public function generateProductLabel($product)
    {
        $barcodePath = 'barcodes/product_' . $product->id . '.png';

        if (!Storage::disk('public')->exists($barcodePath)) {
            $barcodeGenerator = new DNS1D();
            $barcodeData = $barcodeGenerator->getBarcodePNG($product->codebar, 'C128', 2, 50);
            Storage::disk('public')->put($barcodePath, base64_decode($barcodeData));
        }

        $pdf = Pdf::loadView('pdfs.product_label', [
            'product' => $product,
            'barcodePath' => $barcodePath,
        ]);

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
