<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductDetail extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'barcode',
        'type',
        'category_id',
        'product_type_id',
        'brand_id',
        'supplier_id',
        'price',
        'status',


    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getImageUrl()
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return asset('images/img-placeholder.jpg');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_detail_id');
    }


}
