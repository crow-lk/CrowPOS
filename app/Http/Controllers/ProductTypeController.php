<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductTypeStoreRequest;
use App\Models\Category;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            return response(ProductType::with('category')->get()); // Eager load categories
        }
        $productTypes = ProductType::with('category')->latest()->paginate(10);
        return view('productTypes.index')->with('productTypes', $productTypes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all(); // Fetch all categories
    return view('productTypes.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductTypeStoreRequest $request)
    {
        $ProductType = ProductType::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
        ]);

        if (!$ProductType) {
            return redirect()->back()->with('error', __('productType.error_creating'));
        }
        return redirect()->route('productTypes.index')->with('success', __('productType.success_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Http\Response
     */
    public function show(ProductType $productType) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductType $productType)
    {
        $categories = Category::all(); // Fetch all categories
        return view('productTypes.edit', compact('productType', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductType $productType)
    {
        $productType->category_id = $request->category_id;
        $productType->name = $request->name;

        if (!$productType->save()) {
            return redirect()->back()->with('error', __('productType.error_updating'));
        }
        return redirect()->route('productTypes.index')->with('success', __('productType.success_updating'));
    }

    public function destroy(ProductType $productType)
    {
        if ($productType->avatar) {
            Storage::delete($productType->avatar);
        }

        $productType->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
