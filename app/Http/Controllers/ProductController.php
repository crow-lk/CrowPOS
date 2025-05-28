<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\supplier;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::query(); // Start a query on the Product model

        if ($request->search) {
            $products = $products->where('name', 'LIKE', "%{$request->search}%");
        }

        $products = $products->latest()->get(); // Get all products without pagination

        if (request()->wantsJson()) {
            return ProductResource::collection($products);
        }

        return view('products.index')->with('products', $products);
    }

    // public function sendProduct(ProductStoreRequest $request)
    // {
    //     $image_path = '';
    //     $type = $request->input('type');

    //     if ($request->hasFile('image')) {
    //         $image_path = $request->file('image')->store('products', 'public');
    //     }
    //     // Prepare the data to send
    //     $data = [
    //         'id' => $request->id,
    //         'name' => $request->name,
    //         'description' => $request->description,
    //         'category_id' => $request->category_id,
    //         'product_type_id' => $request->product_type_id,
    //         'brand_id' => $request->brand_id,
    //         'supplier_id' => $request->supplier_id,
    //         'image' => $image_path,
    //         'barcode' => $request->barcode,
    //         'price' => $request->price,
    //         'quantity' => $request->quantity,
    //         'status' => $request->status,
    //         'type' => $type,
    //     ];

    //     // Send the data to the external API
    //     $response = Http::post('http://newstore.test/api/products/receive-product', $data);

    //     if ($response->successful()) {
    //         return redirect()->route('products.index')->with('success', __('product.success_creating'));
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'Failed to send stock.'], 500);
    //     }
    // }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $productTypes = ProductType::all();
        $brands = Brand::all();
        $suppliers = Supplier::all(); // Fetch all suppliers
        return view('products.create', compact('categories', 'productTypes', 'brands', 'suppliers'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        $image_path = '';
        $type = $request->input('type');

        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'id' => $request->id,
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'product_type_id' => $request->product_type_id,
            'brand_id' => $request->brand_id,
            'supplier_id' => $request->supplier_id,
            'image' => $image_path,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'type' => $type,
        ]);

        if (!$product) {
            return redirect()->back()->with('error', __('product.error_creating'));
        }
        // else {
        //     $response = $this->sendAdjustment($request);
        //     return $response;
        // }
        return redirect()->route('products.index')->with('success', __('product.success_creating'));
    }

    // public function receiveProduct(Request $request)
    // {
    //     $image_path = '';
    //     $type = $request->input('type');

    //     if ($request->hasFile('image')) {
    //         $image_path = $request->file('image')->store('products', 'public');
    //     }

    //     $product = Product::create([

    //         'name' => $request->name,
    //         'description' => $request->description,
    //         'category_id' => $request->category_id,
    //         'product_type_id' => $request->product_type_id,
    //         'brand_id' => $request->brand_id,
    //         'supplier_id' => $request->supplier_id,
    //         'image' => $image_path,
    //         'barcode' => $request->barcode,
    //         'price' => $request->price,
    //         'quantity' => $request->quantity,
    //         'status' => $request->status,
    //         'type' => $type,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Product Details received successfully.',
    //         'data' => $product,
    //     ], 201);
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
{
    $categories = Category::all();
    $productTypes = ProductType::all();
    $brands = Brand::all();
    $suppliers = Supplier::all(); // Fetch all suppliers
    return view('products.edit', compact('product', 'categories', 'productTypes', 'brands', 'suppliers'));
}


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product->name = $request->name;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->product_type_id = $request->product_type_id;
        $product->brand_id = $request->brand_id;
        $product->supplier_id = $request->supplier_id;
        $product->barcode = $request->barcode;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->status = $request->status;
        $product->type = $request->type;



        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::delete($product->image);
            }
            // Store image
            $image_path = $request->file('image')->store('products', 'public');
            // Save to Database
            $product->image = $image_path;
        }

        if (!$product->save()) {
            return redirect()->back()->with('error', __('product.error_updating'));
        }
        return redirect()->route('products.index')->with('success', __('product.success_updating'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete($product->image);
        }
        $product->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
