<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index($user_id)
    {
        $carts = Cart::where('user_id', $user_id)->get();

        $cartIds = [];
        foreach ($carts as $cart) {
            $cartIds[] = $cart->id;
        }
        $cartItems = CartItem::whereIn('cart_id', $cartIds)->get();

        $productItems = $cartItems->load('product')->map(function ($cartItem) {
            $inStockQuantity = $cartItem->product->in_stock_quantity;

            return [
                'product_id' => $cartItem->product->id,
                'name' => $cartItem->product->name,
                'desc' => $cartItem->product->desc,
                'category' => $cartItem->product->category,
                'unit_weight' => $cartItem->product->unit_weight,
                'price' => $cartItem->product->price,
                'quantity' => $cartItem->quantity >= $inStockQuantity ? $cartItem->quantity : 'Sorry, this product is not in stock',
            ];
        });

        return response()->json([
            'carts' => $carts,
            'cartItems' => $productItems,
        ]);
    }

    public function findById($id)
    {
        $cart = Cart::find($id);

        if (!$cart)
            return response()->json(['message' => 'Cart not found'], 404);

        $cartItems = CartItem::where('cart_id', $cart->id)->get();

        if ($cartItems === null) {
            $productItems = [];
        } else {
            $productItems = $cartItems->load('product')->map(function ($cartItem) {
                $inStockQuantity = $cartItem->product->in_stock_quantity;

                return [
                    'product_id' => $cartItem->product->id,
                    'name' => $cartItem->product->name,
                    'desc' => $cartItem->product->desc,
                    'category' => $cartItem->product->category,
                    'unit_weight' => $cartItem->product->unit_weight,
                    'price' => $cartItem->product->price,
                    'quantity' => $cartItem->quantity >= $inStockQuantity ? $cartItem->quantity : 'Sorry, this product is not in stock',
                ];
            });
        }

        return response()->json([
            'cart' => $cart,
            'cartItems' => $productItems,
        ]);
    }

    public function addProduct(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::find($validatedData['product_id']);

        if (!$product->in_stock_quantity) {
            return response()->json(['message' => 'Product is out of stock'], 400);
        }

        $product->decrement('in_stock_quantity');

        $cart = Cart::firstOrCreate(['user_id' => $validatedData['user_id']]);

        $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            $cartItem = $cart->cartItems()->create([
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }

        return response()->json(['message' => 'Product added to cart'], 201);
    }

    public function removeProduct(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Cart::where('user_id', $validatedData['user_id'])->first();
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cartItem = $cart->cartItems()->where('product_id', $validatedData['product_id'])->first();
        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in cart'], 404);
        }

        $product = Product::find($validatedData['product_id']);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->increment('in_stock_quantity');

        if ($cartItem->quantity > 1) {
            $cartItem->decrement('quantity');
        } else
            $cartItem->delete();

        return response()->json(['message' => 'Product removed from cart'], 200);
    }
}