<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\DeliveryController;


Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/category/{category}', [ProductController::class, 'findByCategory']);
Route::get('/products/{id}', [ProductController::class, 'findById']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::post('/products', [ProductController::class, 'create']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

Route::get('/carts/user/{user_id}', [CartController::class, 'index']);
Route::get('/carts/{id}', [CartController::class, 'findById']);
Route::post('/carts/add', [CartController::class, 'addProduct']);
Route::delete('/carts/remove', [CartController::class, 'removeProduct']);

Route::post('/checkout', [OrderController::class, 'store']);
Route::put('/orders/{id}/payment_status/{status}', [OrderController::class, 'updatePaymentStatus']);
Route::get('/orders/user/{user_id}', [OrderController::class, 'index']);
Route::get('/orders/{id}', [OrderController::class, 'findById']);

Route::post('/subscriptions', [SubscriptionController::class, 'create']);
Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);

Route::put('/deliveries/{id}/{status}', [DeliveryController::class, 'update']);
Route::get('/deliveries/orders/{order_id}', [DeliveryController::class, 'findByOrderId']);
Route::get('/deliveries', [DeliveryController::class, 'index']);