<?php

namespace App\Http\Controllers;

use App\Http\Requests\brandStoreRequest;
use App\Models\ProductType;
use App\Models\brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            return response(Brand::with('productType')->get()); // Eager load productTypes
        }
        $brands = Brand::with('productType')->latest()->paginate(10);
        return view('brands.index')->with('brands', $brands);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $productTypes = ProductType::all(); // Fetch all productTypes
        return view('brands.create', compact('productTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandStoreRequest $request)
    {
        $brand = Brand::create([
            'product_type_id' => $request->product_type_id,
            'name' => $request->name,
        ]);

        if (!$brand) {
            return redirect()->back()->with('error', __('brand.error_creating'));
        }
        return redirect()->route('brands.index')->with('success', __('brand.succes_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(brand $brand)
    {
        $productTypes = ProductType::all(); // Fetch all productTypes
        return view('brands.edit', compact('brand', 'productTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        $brand->product_type_id = $request->product_type_id;
        $brand->name = $request->name;

        if (!$brand->save()) {
            return redirect()->back()->with('error', __('brand.error_updating'));
        }
        return redirect()->route('brands.index')->with('success', __('brand.success_updating'));
    }

    public function destroy(Brand $brand)
    {
        if ($brand->avatar) {
            Storage::delete($brand->avatar);
        }

        $brand->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
