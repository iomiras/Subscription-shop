<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Delivery;
use App\Models\Subscription;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::all();
        return response()->json($deliveries, 200);
    }

    public function findByOrderId($order_id)
    {
        $deliveries = Delivery::where('order_id', $order_id)->get();
        return response()->json($deliveries, 200);
    }

    public function update(int $id, $status)
    {
        $delivery = Delivery::find($id);
        $previousStatus = $delivery->status;
        $delivery->status = $status;
        $delivery->save();

        if ($previousStatus !== 'delivered' && $status === 'delivered') {
            $currentDate = Carbon::now()->timezone('Asia/Almaty');
            $delivery->delivery_timestamp = $currentDate;

            $subscription = Subscription::where('order_id', $delivery->order_id)->first();
            $subscriptionEndDate = Carbon::parse($subscription->current_period_end);
            $nextDeliveryDate = $currentDate->addWeek();

            if ($nextDeliveryDate->greaterThanOrEqualTo($subscriptionEndDate)) {
                return response()->json(['message' => 'Delivery status updated successfully, but cannot create new delivery after subscription ends'], 422);
            }

            $newDelivery = new Delivery();
            $newDelivery->order_id = $delivery->order_id;
            $newDelivery->planned_delivery_date = $nextDeliveryDate->format('Y-m-d');
            $newDelivery->planned_time_slot = $delivery->time_slot;
            $newDelivery->status = 'pending';

            $newDelivery->save();
            return response()->json(['message' => 'Delivery status updated successfully and new delivery entry created'], 201);
        }

        return response()->json(['message' => 'Delivery status updated successfully'], 200);
    }
}