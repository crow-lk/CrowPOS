<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stockMovements = StockMovement::with(['supplier', 'fromStore', 'toStore', 'store'])
            ->where('store_id', $user->store_id)
            ->whereIn('movement_type', ['stock_in', 'stock_out']) // Filter for stock_in and stock_out
            ->paginate(5);
        // Adjustment movements (without store_id filter)
        $adjustmentMovements = StockMovement::with(['supplier', 'fromStore', 'toStore', 'store'])
            ->where('movement_type', 'adjustment') // Filter for adjustments
            ->paginate(5);

        return view('stock_movements.index', compact('stockMovements', 'adjustmentMovements'));
    }

    public function create()
    {
        $user = auth()->user();
        $productDetails = ProductDetail::all();
        $suppliers = Supplier::all();
        $stores = Store::all();

        return view('stock_movements.create', compact('productDetails', 'suppliers', 'stores'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'movement_type' => 'required|in:stock_in,stock_out,adjustment',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'products' => 'required|array',
            'reason' => 'nullable|string',
            'cost_prices' => 'required|array',
            'quantities' => 'required|array',
            'cost_prices.*' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
            'quantities.*' => 'integer|min:1',
            'from_store_id' => 'nullable|integer|exists:stores,id',
            'to_store_id' => 'nullable|integer|exists:stores,id',
            'store_id' => 'nullable|integer|exists:stores,id',
        ]);

        DB::beginTransaction();

        try {
            $stockMovement = StockMovement::create([
                'movement_type' => $request->movement_type,
                'supplier_id' => $request->supplier_id,
                'products' => json_encode($request->products),
                'reason' => $request->reason,
                'quantities' => json_encode($request->quantities),
                'cost_prices' => json_encode($request->cost_prices),
                'from_store_id' => $request->from_store_id,
                'to_store_id' => $request->to_store_id,
                'store_id' => $user->store_id,
            ]);

            $product = Product::create([

            ]);

            foreach ($request->products as $index => $productDetailId) {
                $quantity = $request->quantities[$index];

                if ($request->movement_type === 'stock_in') {
                    // Stock In: Increase quantity in the current store
                    $product = Product::where('product_detail_id', $productDetailId)
                        ->where('store_id', $user->store_id)
                        ->first();
                    if ($product) {
                        $product->increment('quantity', $quantity);
                    } else {
                        // Create a new product entry if it doesn't exist
                        Product::create([
                            'product_detail_id' => $productDetailId,
                            'store_id' => $user->store_id,
                            'quantity' => $quantity,
                        ]);
                    }
                } elseif ($request->movement_type === 'stock_out') {
                    // Stock Out: Decrease quantity in the current store
                    $product = Product::where('product_detail_id', $productDetailId)
                        ->where('store_id', $user->store_id)
                        ->first();

                    if ($product) {
                        $product->decrement('quantity', $quantity);
                    }
                } elseif ($request->movement_type === 'adjustment') {
                    // Adjustment: Decrease from_store and increase to_store

                    // Decrease quantity in from_store
                    $fromProduct = Product::where('product_detail_id', $productDetailId)
                        ->where('store_id', $request->from_store_id)
                        ->first();

                    if ($fromProduct) {
                        $fromProduct->decrement('quantity', $quantity);
                    }

                    // Increase quantity in to_store
                    $toProduct = Product::where('product_detail_id', $productDetailId)
                        ->where('store_id', $request->to_store_id)
                        ->first();

                    if ($toProduct) {
                        $toProduct->increment('quantity', $quantity);
                    } else {
                        // Create a new product entry in to_store if it doesn't exist
                        Product::create([
                            'product_detail_id' => $productDetailId,
                            'store_id' => $request->to_store_id,
                            'quantity' => $quantity,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('stock_movements.index')->with('success', 'Stock movement created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create stock movement.  See logs for details.');
        }
    }

    public function destroy(StockMovement $stockMovement)
    {
        $stockMovement->delete();

        return redirect()->route('stock_movements.index')->with('success', 'Stock movement deleted successfully.');
    }
}
