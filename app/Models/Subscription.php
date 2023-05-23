<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'status',
        'current_period_start',
        'current_period_end',
        'address',
        'preferred_day',
        'time_slot',
    ];

    function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}