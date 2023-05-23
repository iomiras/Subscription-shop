<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function findByCategory(string $category)
    {
        return Product::where('category', $category)->get();
    }

    public function findById(int $id)
    {
        return Product::where('id', $id)->first();
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'desc' => 'required|string',
            'price' => 'required|integer',
            'category' => 'required|string',
            'unit_weight' => 'required|numeric',
            'in_stock_quantity' => 'required|integer',
        ]);

        $product = new Product();
        $product->name = $validatedData['name'];
        $product->desc = $validatedData['desc'];
        $product->price = $validatedData['price'];
        $product->category = $validatedData['category'];
        $product->unit_weight = $validatedData['unit_weight'];
        $product->in_stock_quantity = $validatedData['in_stock_quantity'];
        $product->save();

        return response()->json(['message' => 'Product created successfully'], 201);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update($request->all());

        return response()->json(['message' => 'Product updated successfully']);
    }

    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}