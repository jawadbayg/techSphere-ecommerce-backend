<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function getOrders(Request $request)
    {
        
        $orders = Order::all();
        
        
        $totalOrders = $orders->count();
        
        
        return response()->json([
            'total_orders' => $totalOrders,
            'orders' => $orders
        ], 200);
    }


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

    public function updateOrderStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,in process,dispatched,delivered'
        ]);

        $order = Order::findOrFail($id);
        
        $order->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Order status updated successfully!',
            'order' => $order
        ], 200);
    }

    public function destroy($id)
    {
        $orders = Order::findOrFail($id);
        $orders->delete();
        return response()->json(['message' => 'Order deleted successfully!'], 200);
    }

    public function placeOrderfromCart(Request $request)
{
    
    $request->validate([
        'cart_ids' => 'required|array', 
        'cart_ids.*' => 'exists:carts,id', 
    ]);

    $userId = Auth::id();
    $totalPrice = 0;

    
    $cartItems = Cart::whereIn('id', $request->cart_ids)->where('user_id', $userId)->get();

    
    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'No items in cart to order.'], 400);
    }

    
    foreach ($cartItems as $cartItem) {
        
        Order::create([
            'user_id' => $userId,
            'product_unique_id' => $cartItem->product_unique_id,
            'quantity' => $cartItem->quantity,
            'total_price' => $cartItem->total_price,
            'status' => 'pending', 
        ]);

        
        $totalPrice += $cartItem->total_price;

        
        $cartItem->delete();
    }

    return response()->json([
        'message' => 'Order placed successfully!',
        'total_price' => $totalPrice,
    ], 201);
}

}
