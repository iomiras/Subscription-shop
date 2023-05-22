<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;
use App\Models\Product;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];


    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}