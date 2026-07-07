<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        $data = $request->only([
            'name',
            'price',
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
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        $data = $request->only([
            'name',
            'price',
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
