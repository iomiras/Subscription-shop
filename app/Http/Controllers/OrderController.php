<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Validator;


class OrderController extends Controller
{
    public function index($user_id)
    {
        $orders = Order::where('user_id', $user_id)->get();

        $orderIds = [];
        foreach ($orders as $order) {
            $orderIds[] = $order->id;
        }
        $orderItems = OrderItem::whereIn('order_id', $orderIds)->get();

        $productItems = $orderItems->load('product')->map(function ($orderItem) {
            $inStockQuantity = $orderItem->product->in_stock_quantity;

            return [
                'product_id' => $orderItem->product->id,
                'name' => $orderItem->product->name,
                'desc' => $orderItem->product->desc,
                'category' => $orderItem->product->category,
                'unit_weight' => $orderItem->product->unit_weight,
                'price' => $orderItem->product->price,
                'quantity' => $orderItem->quantity >= $inStockQuantity ? $orderItem->quantity : 'Sorry, this product is not in stock',
            ];
        });

        return response()->json([
            'orders' => $orders,
            'orderItems' => $productItems,
        ]);
    }

    public function findById($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $orderItems = OrderItem::where('order_id', $order->id)->get();

        if ($orderItems === null)
            $productItems = [];
        else {
            $productItems = $orderItems->load('product')->map(
                function ($orderItem) {
                    $inStockQuantity = $orderItem->product->in_stock_quantity;
                    return [
                        'product_id' => $orderItem->product->id,
                        'name' => $orderItem->product->name,
                        'desc' => $orderItem->product->desc,
                        'category' => $orderItem->product->category,
                        'unit_weight' => $orderItem->product->unit_weight,
                        'price' => $orderItem->product->price,
                        'quantity' => $orderItem->quantity >= $inStockQuantity ? $orderItem->quantity : 'Sorry, this product is not in stock',
                    ];
                }
            );
        }

        return response()->json([
            'order' => $order,
            'orderItems' => $productItems,
        ]);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $cart = Cart::where('user_id', $validatedData['user_id'])->first();
        $cartItems = $cart->cartItems;

        $total = 0;
        foreach ($cartItems as $cartItem) {
            $itemPrice = $cartItem->product->category === 'dairy'
                ? $cartItem->product->price * $cartItem->quantity
                : $cartItem->product->unit_weight * $cartItem->product->price * $cartItem->quantity;

            $total += $itemPrice;
        }

        $order = new Order();
        $order->user_id = $validatedData['user_id'];
        $order->total = $total;
        $order->payment_status = 'billed';
        $order->save();

        foreach ($cartItems as $cartItem) {
            $order->orderItems()->create([
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
            ]);
        }

        $cart->cartItems()->delete();
        $cart->delete();

        return response()->json(['message' => 'Order created successfully']);
    }

    public function updatePaymentStatus($id, $status)
    {
        $validator = Validator::make(['status' => $status], [
            'status' => 'required|in:billed,paid',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        $order = Order::where('id', $id)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->payment_status === $status)
            return response()->json(['message' => 'Payment status is already set to this']);
        $order->payment_status = $status;
        $order->save();

        return response()->json(['message' => 'Payment status updated successfully']);
    }

}