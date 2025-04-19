<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index()
    {
        // Retrieve all stock movements, you can also paginate if needed
        $stockMovements = StockMovement::with('supplier')->paginate(10); // Adjust the pagination as needed

        return view('stock_movements.index', compact('stockMovements'));
    }
    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        return view('stock_movements.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'movement_type' => 'required|in:stock_in,stock_out,adjustment', // Validate against enum values
            'supplier_id' => 'required|exists:suppliers,id',
            'products' => 'required|array',
            'reason' => 'nullable|string',
        ]);

        StockMovement::create([
            'movement_type' => $request->movement_type,
            'supplier_id' => $request->supplier_id,
            'products' => json_encode($request->products), // Store product IDs as JSON
            'reason' => $request->reason,
        ]);

        return redirect()->route('stock_movements.index')->with('success', 'Stock movement created successfully.');
    }

    //delete stock movement
    public function destroy(StockMovement $stockMovement)
    {
        $stockMovement->delete();

        return redirect()->route('stock_movements.index')->with('success', 'Stock movement deleted successfully.');
    }
}
