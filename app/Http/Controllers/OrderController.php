<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_unique_id' => 'required|string|exists:products,product_unique_id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        $user = Auth::user();
        $product = Product::where('product_unique_id', $validated['product_unique_id'])->first();
    
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }
    
        if (!$product->in_stock) {
            return response()->json(['message' => 'Product is out of stock.'], 400);
        }

        if ($validated['quantity'] > $product->quantity_available) {
            return response()->json(['message' => 'Insufficient stock.'], 400);
        }
    
        $totalPrice = $product->price * $validated['quantity'];
    
        $order = Order::create([
            'user_id' => $user->id,
            'product_unique_id' => $product->product_unique_id,
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);
    
        $product->quantity_available -= $validated['quantity'];
        if ($product->quantity_available <= 0) {
            $product->in_stock = false;
        }
        $product->save();
    
        return response()->json([
            'message' => 'Order placed successfully!',
            'order' => $order,
        ], 201);
    }
}
