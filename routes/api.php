<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{category}', [ProductController::class, 'index']);

Route::get('/users', [UserController::class, 'index']);

// Route::post('cart/add', [CartItemsController::class, 'store']);
Route::get('/cart/{user_id}', [CartController::class, 'index']);
Route::post('/cart/add', [CartController::class, 'store']);
Route::delete('/cart/remove', [CartController::class, 'removeProduct']);