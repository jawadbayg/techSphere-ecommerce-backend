<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;



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

    public function outOfStock()
    {
    $outOfStockProducts = Product::where('quantity_available', 0)->get();

    return response()->json([
        'out_of_stock_products' => $outOfStockProducts
    ], 200);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_unique_id' => 'required|exists:products,product_unique_id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::where('product_unique_id', $request->product_unique_id)->first();

        $cart = Cart::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_unique_id' => $product->product_unique_id,
            ],
            [
                'quantity' => $request->quantity,
                'total_price' => $product->price * $request->quantity
            ]
        );

        return response()->json([
            'message' => 'Product added to cart successfully.',
            'cart' => $cart
        ], 200);
    }

    
    public function viewCart()
    {
        $cartItems = Cart::where('user_id', Auth::id())->get();

        return response()->json([
            'cart_items' => $cartItems
        ], 200);
    }

    
    public function removeFromCart($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->first();

        if ($cartItem) {
            $cartItem->delete();
            return response()->json(['message' => 'Product removed from cart.'], 200);
        }

        return response()->json(['message' => 'Item not found in cart.'], 404);
    }

}
