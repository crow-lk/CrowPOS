<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Supplier;
use App\Models\ProductType;
use Illuminate\Http\Request;
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
        $user = auth()->user(); // Get the logged-in user
        $products = Product::with('productDetail')->where('store_id', $user->store_id); // Filter products by user's store_id

        if ($request->barcode) {
            $products = $products->whereHas('productDetail', function($query) use ($request) {
                $query->where('barcode', $request->barcode);
            });
        }
        if ($request->search) {
            $products = $products->whereHas('productDetail', function($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->search}%");
            });
        }
        if ($request->wantsJson()) {
            $productsData = $products->get()->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->productDetail->name,
                    'description' => $product->productDetail->description,
                    'image_url' => $product->productDetail->getImageUrl(),
                    'barcode' => $product->productDetail->barcode,
                    'type' => $product->productDetail->type,
                    'price' => $product->productDetail->price,
                    'quantity' => $product->quantity,
                ];
            });
            return response()->json(['data' => $productsData]);
        }
        return view('products.index')->with('products', $products);
    }


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
        $suppliers = Supplier::all();
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

        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('products', 'public');
        }
        $product = ProductDetail::create(
            [
                'name' => $request->name,
                'description' => $request->description,
                'image' => $image_path, // Handle image upload if necessary
                'barcode' => $request->barcode,
                'type' => $request->type,
                'category_id' => $request->category_id,
                'product_type_id' => $request->product_type_id,
                'brand_id' => $request->brand_id,
                'supplier_id' => $request->supplier_id,
                'price' => $request->price,
                'status' => $request->status,
            ]
        );
        if (!$product) {
            return redirect()->back()->with('error', __('product.error_creating'));
        }

        $user = auth()->user();
        if ($request->type === 'service') {
            Product::create([
                'product_detail_id' => $product->id,
                'store_id' => $user->store_id,
                'quantity' => 1,
            ]);
        }

        return redirect()->route('products.index')->with('success', __('product.success_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $user = auth()->user();
        if ($product->store_id !== $user->store_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductDetail $product)
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
    public function update(ProductUpdateRequest $request, ProductDetail $product)
    {
        $product->name = $request->name;
        $product->description = $request->description;
        $product->image = $request->image; // Handle image upload if necessary
        $product->barcode = $request->barcode;
        $product->type = $request->type;
        $product->category_id = $request->category_id;
        $product->product_type_id = $request->product_type_id;
        $product->brand_id = $request->brand_id;
        $product->supplier_id = $request->supplier_id;
        $product->price = $request->price;
        $product->status = $request->status;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::delete($product->image);
            }
            // Store new image
            $image_path = $request->file('image')->store('products', 'public');
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
    public function destroy(ProductDetail $product)
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
