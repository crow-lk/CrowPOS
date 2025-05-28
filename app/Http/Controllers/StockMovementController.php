<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

    public function sendAdjustment(Request $request)
    {
        $request->validate([
            'from_store_id' => 'required|integer|exists:stores,id',
            'to_store_id' => 'required|integer|exists:stores,id',
            'products' => 'required|array',
            'quantities' => 'required|array',
            'cost_prices' => 'required|array',
        ]);

        // Check if the from_store_id is an admin store
        $fromStore = Store::find($request->from_store_id);
        if (!$fromStore->is_admin) {
            return response()->json(['error' => 'From store must be an admin store.'], 403);
        }

        // Prepare the data to send
        $data = [
            'movement_type' => 'adjustment',
            'from_store_id' => $request->from_store_id,
            'to_store_id' => $request->to_store_id,
            'products' => $request->products,
            'quantities' => $request->quantities,
            'cost_prices' => $request->cost_prices,
        ];

        // Send the data to the external API
        $response = Http::post('http://newstore.test/api/stock_movements/receive-adjustment', $data);

        if ($response->successful()) {
            // Decrease the quantities in the fromStore
            foreach ($request->products as $index => $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $product->decrement('quantity', $request->quantities[$index]);
                }
            }
            return redirect()->route('stock_movements.index')->with('success', 'Stock movement created and sent successfully.');
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to send stock.'], 500);
        }
    }

    public function store(Request $request)
    {
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
        ]);

        // Custom validation for stock_out and adjustment movement types
        if (in_array($request->movement_type, ['stock_out', 'adjustment'])) {
            foreach ($request->products as $index => $productId) {
                $product = Product::find($productId);
                if ($product && $request->quantities[$index] > $product->quantity) {
                    return redirect()->back()->withErrors([
                        'quantities.*' => "The quantity exceeds the available stock."
                    ])->withInput();
                }
            }
        }

        // Create the stock movement
        $stockMovement = StockMovement::create([
            'movement_type' => $request->movement_type,
            'supplier_id' => $request->supplier_id,
            'products' => json_encode($request->products),
            'reason' => $request->reason,
            'quantities' => json_encode($request->quantities),
            'cost_prices' => json_encode($request->cost_prices),
            'from_store_id' => $request->from_store_id,
            'to_store_id' => $request->to_store_id,
        ]);

        // Handle stock movement based on type
        if ($request->movement_type === 'stock_in') {
            foreach ($request->products as $index => $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $product->increment('quantity', $request->quantities[$index]);
                }
            }
        } elseif ($request->movement_type === 'stock_out') {
            foreach ($request->products as $index => $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $product->decrement('quantity', $request->quantities[$index]);
                }
            }
        } elseif ($request->movement_type === 'adjustment') {
            // Call sendAdjustment method
            $response = $this->sendAdjustment($request);
            return $response; // Return the response from sendAdjustment
        }

        return redirect()->route('stock_movements.index')->with('success', 'Stock movement created successfully.');
    }

    public function receiveAdjustment(Request $request)
    {
        $request->validate([
            'from_store_id' => 'required|integer|exists:stores,id',
            'to_store_id' => 'required|integer|exists:stores,id',
            'products' => 'required|array',
            'quantities' => 'required|array',
            'cost_prices' => 'required|array',
        ]);

        // Check if the to_store_id is an admin store
        $toStore = Store::find($request->to_store_id);
        if (!$toStore->is_admin) {
            return response()->json(['error' => 'To store must be an admin store.'], 403);
        }

        // Create the stock movement
        $stockMovement = StockMovement::create([
            'movement_type' => 'adjustment',
            'from_store_id' => $request->from_store_id,
            'to_store_id' => $request->to_store_id,
            'products' => json_encode($request->products),
            'quantities' => json_encode($request->quantities),
            'cost_prices' => json_encode($request->cost_prices),
        ]);

        // Increase the quantity of products in the to_store
        foreach ($request->products as $index => $productId) {
            $product = Product::find($productId);
            if ($product) {
                // Increment the quantity in the to_store
                $product->increment('quantity', $request->quantities[$index]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock movement adjustment received successfully.',
            'data' => $stockMovement,
        ], 201);
    }

    // Delete stock movement
    public function destroy(StockMovement $stockMovement)
    {
        $stockMovement->delete();

        return redirect()->route('stock_movements.index')->with('success', 'Stock movement deleted successfully.');
    }
}


