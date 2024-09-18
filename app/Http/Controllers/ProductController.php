<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; 
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Store a newly created product in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'in_stock' => 'required|boolean',
            'image' => 'required|string', 
        ]);

        
        $productUniqueId = Str::slug($validated['title'], '-') . '-' . rand(1000, 9999);

        
        $product = Product::create([
            'product_unique_id' => $productUniqueId,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'in_stock' => $validated['in_stock'],
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
        'in_stock' => 'required|boolean',
        'image' => 'nullable|string', 
    ]);

    
    $product = Product::findOrFail($id);

    
    $product->update([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'price' => $validated['price'],
        'in_stock' => $validated['in_stock'],
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
