<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductImage;

class ShopController extends Controller
{
    // gawin nyo nalang yung mga nasa list jan  
    // Add product to cart
    public function addToCart(Request $request){
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'quantity'   => 'required|integer|min:1',
            'product_id' => 'required|integer|exists:products,product_id' // ✅
        ]);

        $product = Product::find($request->product_id);

        $cart = Cart::create([
            'quantity'   => $request->quantity,
            'product_id' => $request->product_id,
            'user_id'    => $user->id,                              // ✅ matches migration
            'total'      => $product->price * $request->quantity,  // ✅ auto compute
            'status'     => 'pending'
        ]);

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart'    => $cart
        ], 201);
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'category_id' => 'required|integer',
            'product_stocks' => 'required|integer',
            'description' => 'required|string',
            'price' => 'required|numeric',
            
            'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $isSale = $request->boolean('isSale');

        // Store image
        $imagePath = $request->file('image')->store('products', 'public');

        $product = Product::create([
            'product_name' => $request->product_name,
            'category_id' => $request->category_id,
            'product_stocks' => $request->product_stocks,
            'description' => $request->description,
            'price' => $request->price,
            'isSale' => $isSale,
            'image' => $imagePath
        ]);

        return response()->json([
            'message' => 'Product added successfully',
            'data' => $product
        ]);
    }

 