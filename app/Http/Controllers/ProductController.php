<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
   public function index(Request $request)
{
    $query = Product::with('category');

    // Search Product
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Filter Category
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    $products = $query->latest()->paginate(10)->withQueryString();

    $categories = Category::where('status', 1)->get();

    return view('products.index', compact('products', 'categories'));
}

 public function create()
{
    $categories = Category::where('status', 1)->get();

    return view('products.create', compact('categories'));
}

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',
            'price' => 'required|numeric',
            'cost_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        $data = $request->only([
            'name',
            'price',
            'cost_price',
            'barcode',
            'stock',
            'category_id'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $rows = IOFactory::load($request->file('file')->getRealPath())
            ->getActiveSheet()
            ->toArray(null, true, true, true);

        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), array_shift($rows));
        $created = 0;

        foreach ($rows as $row) {
            $record = array_combine($headers, $row);
            if (empty(trim((string) ($record['name'] ?? '')))) {
                continue;
            }

            $categoryName = trim((string) ($record['category'] ?? 'General')) ?: 'General';
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['description' => null, 'status' => 1]
            );

            Product::create([
                'name' => trim($record['name']),
                'barcode' => trim((string) ($record['barcode'] ?? '')) ?: null,
                'price' => (float) ($record['selling price'] ?? $record['price'] ?? 0),
                'cost_price' => (float) ($record['cost price'] ?? 0),
                'stock' => (int) ($record['stock'] ?? 0),
                'category_id' => $category->id,
            ]);
            $created++;
        }

        return redirect()->route('products.index')->with('success', "Imported {$created} products successfully.");
    }

  public function edit(Product $product)
{
    $categories = Category::where('status', 1)->get();

    return view('products.edit', compact('product', 'categories'));
}

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'cost_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        $data = $request->only([
            'name',
            'price',
            'cost_price',
            'barcode',
            'stock',
            'category_id'
        ]);

        if ($request->hasFile('image')) {

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
