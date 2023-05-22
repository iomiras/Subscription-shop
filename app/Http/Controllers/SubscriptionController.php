<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\Subscription;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Retrieve all subscriptions
    }

    public function store(Request $request)
    {
        // Create a new subscription
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'address' => 'required|string',
            'preferred_day' => 'required|string',
            'time_slot' => 'required|integer',
        ]);


        $subscription = new Subscription();
        $subscription->user_id = $validatedData['user_id'];
        $subscription->order_id = $validatedData['order_id'];
        $subscription->status = 'active';

        $currentDate = Carbon::now();
        $subscription->current_period_start = $currentDate->format('Y-m-d');

        $currentPeriodEnd = $currentDate->addDays(30);
        $subscription->current_period_end = $currentPeriodEnd->format('Y-m-d');

        $subscription->address = $validatedData['address'];
        $subscription->preferred_day = $validatedData['preferred_day'];
        $subscription->time_slot = $validatedData['time_slot'];

        $order = Order::findOrFail($validatedData['order_id']);
        $order->payment_status = 'paid';
        $order->save();

        $subscription->save();

        return response()->json(['message' => 'Subscription created successfully'], 201);
    }

    public function show($id)
    {
        $subscription = Subscription::find($id);
        if ($subscription)
            return response()->json($subscription, 200);
        else
            return response()->json(['message' => 'Subscription not found'], 404);
    }


    public function update(Request $request, $id)
    {
        // Update the details of a subscription
    }

    public function destroy($id)
    {
        // Delete a subscription
    }

}