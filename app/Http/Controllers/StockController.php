<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockStoreRequest;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            return response(
                Supplier::all()
            );
        }
        $stocks = Supplier::latest()->paginate(10);
        return view('stocks.index')->with('stocks', $stocks);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('stocks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $logo_path = '';

        if ($request->hasFile('logo')) {
            $logo_path = $request->file('logo')->store('stocks', 'public');
        }

        $stock = Supplier::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $logo_path,
        ]);

        if (!$stock) {
            return redirect()->back()->with('error', __('stock.error_creating'));
        }
        return redirect()->route('stocks.index')->with('success', __('stock.success_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Stock $stock)
    {
        // Implement logic to show details of a specific stock
        return view('stocks.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $stock
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $stock)
    {
        return view('stocks.edit', compact('stock'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $stock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $stock)
    {
        $stock->name = $request->name;
        $stock->email = $request->email;
        $stock->phone = $request->phone;
        $stock->address = $request->address;

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($stock->logo) {
                Storage::delete($stock->logo);
            }
            // Store new logo
            $logo_path = $request->file('logo')->store('stocks', 'public');
            // Save to Database
            $stock->logo = $logo_path;
        }

        if (!$stock->save()) {
            return redirect()->back()->with('error', __('stock.error_updating'));
        }
        return redirect()->route('stocks.index')->with('success', __('stock.success_updating'));
    }

    public function destroy(Supplier $stock)
    {
        if ($stock->logo) {
            Storage::delete($stock->logo);
        }

        $stock->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
