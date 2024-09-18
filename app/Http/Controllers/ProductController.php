<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'products' => $products
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'in_stock' => 'required|boolean',
            'quantity_available' => 'required|integer|min:0', 
            'image' => 'required|string',
        ]);

        $productUniqueId = Str::slug($validated['title'], '-') . '-' . rand(1000, 9999);

        
        $inStock = $validated['quantity_available'] > 0 ? true : false;

        $product = Product::create([
            'product_unique_id' => $productUniqueId,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'in_stock' => $inStock, 
            'quantity_available' => $validated['quantity_available'],
            'image' => $validated['image'],
        ]);

        return response()->json([
            'message' => 'Product created successfully!',
            'product' => $product,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'in_stock' => 'nullable|boolean',
            'quantity_available' => 'nullable|integer|min:0', 
            'image' => 'nullable|string',
        ]);

        $product = Product::findOrFail($id);

        
        $inStock = isset($validated['quantity_available']) ? $validated['quantity_available'] > 0 : $product->in_stock;

        $product->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'in_stock' => $inStock, 
            'quantity_available' => $validated['quantity_available'] ?? $product->quantity_available,
            'image' => $validated['image'] ?? $product->image,
        ]);

        return response()->json(['message' => 'Product updated successfully!', 'product' => $product], 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully!'], 200);
    }
}
