<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoreRequest;
use App\Models\ProductType;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
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
                Store::all()
            );
        }
        $stores = Store::latest()->paginate(10);
        return view('stores.index')->with('stores', $stores);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $productTypes = ProductType::all(); // Fetch all productTypes
        return view('stores.create', );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStoreRequest $request)
    {
        $Store = Store::create([
            'name' => $request->name,
            'is_admin' => $request->is_admin,
        ]);

        if (!$Store) {
            return redirect()->back()->with('error', __('store.error_creating'));
        }
        return redirect()->route('stores.index')->with('success', __('store.success_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store  $Store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $Store) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Store  $Store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $Store)
    {
        // $productTypes = ProductType::all(); // Fetch all productTypes
        return view('stores.edit',compact('store'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $Store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $Store)
    {
        // $Store->product_type_id = $request->product_type_id;
        $Store->name = $request->name;

        if (!$Store->save()) {
            return redirect()->back()->with('error', __('store.error_updating'));
        }
        return redirect()->route('stores.index')->with('success', __('store.success_updating'));
    }

    public function destroy(Store $Store)
    {
        if ($Store->avatar) {
            Storage::delete($Store->avatar);
        }

        $Store->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
