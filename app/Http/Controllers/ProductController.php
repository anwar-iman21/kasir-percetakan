<?php

namespace App\Http\Controllers;

use App\Models\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductService::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $products = $query->ordered()->paginate(20)->withQueryString();
        $categories = ProductService::getCategories();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ProductService::getCategories();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        ProductService::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
            'unit' => $request->unit,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk/Jasa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $product = ProductService::findOrFail($id);
        $categories = ProductService::getCategories();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $product = ProductService::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
            'unit' => $request->unit,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk/Jasa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        ProductService::findOrFail($id)->delete();
        return redirect()->route('products.index')->with('success', 'Produk/Jasa berhasil dihapus.');
    }

    public function show($id)
    {
        $product = ProductService::findOrFail($id);
        return view('products.show', compact('product'));
    }
}
