<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json($request->user()->cart()->get());
        }
        return view('cart.index');
    }

    public function store(Request $request)
    {

        $barcode = $request->barcode;

        // Retrieve the product based on the barcode
        $product = Product::with('productDetail')->whereHas('productDetail', function($query) use ($barcode) {
            $query->where('barcode', $barcode);
        })->first();

        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => __('cart.product_not_found')], 404);
        }

        // Check if the product is already in the cart
        $cart = $request->user()->cart()->where('product_id', $product->id)->first();
        if ($cart) {
            // Check product quantity
            if ($product->quantity <= $cart->pivot->quantity) {
                return response()->json([
                    'message' => __('cart.available', ['quantity' => $product->quantity]),
                ], 400);
            }
            // Update only quantity
            $cart->pivot->quantity += 1;
            $cart->pivot->save();
        } else {
            // Check if the product is in stock
            if ($product->quantity < 1) {
                return response()->json([
                    'message' => __('cart.outstock'),
                ], 400);
            }
            // Add the product to the cart
            $request->user()->cart()->attach($product->id, ['quantity' => 1]);
        }

        return response()->json([], 204);
    }

    public function changeQty(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);
        $cart = $request->user()->cart()->where('product_id', $request->product_id)->first();

        if ($cart) {
            // Check product quantity
            if ($product->quantity < $request->quantity) {
                return response()->json([
                    'message' => __('cart.available', ['quantity' => $product->quantity]),
                ], 400);
            }
            $cart->pivot->quantity = $request->quantity;
            $cart->pivot->save();
        }

        return response()->json(['success' => true]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);
        $request->user()->cart()->detach($request->product_id);

        return response()->json([], 204);
    }

    public function empty(Request $request)
    {
        $request->user()->cart()->detach();

        return response()->json([], 204);
    }
}
