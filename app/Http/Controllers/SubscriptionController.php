<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Delivery;
use App\Models\Subscription;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'address' => 'required|string',
            'preferred_day' => 'required|string',
            'time_slot' => 'required|integer',
        ]);

        $timeSlots = [
            1 => '08:00 - 11:00',
            2 => '11:00 - 14:00',
            3 => '14:00 - 17:00',
            4 => '17:00 - 20:00',
        ];

        $dayMap = [
            'Mon' => 'Monday',
            'Tue' => 'Tuesday',
            'Wed' => 'Wednesday',
            'Thu' => 'Thursday',
            'Fri' => 'Friday',
            'Sat' => 'Saturday',
            'Sun' => 'Sunday',
        ];
        $preferredDay = $dayMap[$validatedData['preferred_day']];

        $subscription = new Subscription();
        $subscription->user_id = $validatedData['user_id'];
        $subscription->order_id = $validatedData['order_id'];
        $subscription->status = 'active';

        $currentDate = Carbon::now()->timezone('Asia/Almaty');
        $subscription->current_period_start = $currentDate->format('Y-m-d');

        $currentPeriodEnd = $currentDate->addDays(30);
        $subscription->current_period_end = $currentPeriodEnd->format('Y-m-d');

        $subscription->address = $validatedData['address'];
        $subscription->preferred_day = $validatedData['preferred_day'];
        $subscription->time_slot = $validatedData['time_slot'];

        $subscription->save();

        $order = Order::findOrFail($validatedData['order_id']);
        $order->payment_status = 'paid';

        $order->save();

        $validatedData['time_slot'] = $timeSlots[$validatedData['time_slot']];

        $delivery = new Delivery();
        $delivery->order_id = $validatedData['order_id'];
        $delivery->planned_delivery_date = date('Y-m-d', strtotime('next ' . $preferredDay));
        $delivery->planned_time_slot = $validatedData['time_slot'];
        $delivery->status = 'pending';

        $delivery->save();

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
}