<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\Store;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index()
    {
        // Retrieve all stock movements with supplier, fromStore, and toStore, paginated
        $stockMovements = StockMovement::with(['supplier', 'fromStore', 'toStore'])->paginate(10);

        return view('stock_movements.index', compact('stockMovements'));
    }

    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        $stores = Store::all();

        return view('stock_movements.create', compact('products', 'suppliers', 'stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'movement_type' => 'required|in:stock_in,stock_out,adjustment', // Validate against enum values
            'supplier_id' => 'nullable|integer|exists:suppliers,id', // nullable for adjustments
            'products' => 'required|array',
            'reason' => 'nullable|string',
            'cost_price' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
            'quantity' => 'required|integer|min:1',
            'from_store_id' => 'nullable|integer|exists:stores,id',
            'to_store_id' => 'nullable|integer|exists:stores,id',
        ]);

        // Create the stock movement
        $stockMovement = StockMovement::create([
            'movement_type' => $request->movement_type,
            'supplier_id' => $request->supplier_id,
            'products' => json_encode($request->products), // Store product IDs as JSON
            'reason' => $request->reason,
            'quantity' => $request->quantity,
            'cost_price' => $request->cost_price,
            'from_store_id' => $request->from_store_id,
            'to_store_id' => $request->to_store_id,
        ]);

        // Update product quantities based on movement type
        foreach ($request->products as $productId) {
            $product = Product::find($productId);
            if ($product) {
                if ($request->movement_type === 'stock_in') {
                    $product->increment('quantity', $request->quantity); // Increase quantity
                } elseif ($request->movement_type === 'stock_out') {
                    $product->decrement('quantity', $request->quantity); // Decrease quantity
                }
            }
        }

        return redirect()->route('stock_movements.index')->with('success', 'Stock movement created successfully.');
    }

    // Delete stock movement
    public function destroy(StockMovement $stockMovement)
    {
        $stockMovement->delete();

        return redirect()->route('stock_movements.index')->with('success', 'Stock movement deleted successfully.');
    }
}


