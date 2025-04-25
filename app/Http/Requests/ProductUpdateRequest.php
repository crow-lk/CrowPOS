<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $product_id = $this->route('product')->id;
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id',
            'product_type_id' => 'required|integer|exists:product_types,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'image' => 'nullable|image',
            'barcode' => 'nullable|string|max:50',
            'price' => 'regex:/^\d+(\.\d{1,2})?$/',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'quantity' => 'required|integer',
            'status' => 'required|boolean',
            'type' => 'required|in:product,service',
        ];
    }
}
