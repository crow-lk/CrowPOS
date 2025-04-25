<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'product_type_id',
        'brand_id',
        'supplier_id',
        'image',
        'barcode',
        'price',
        'quantity',
        'status',
        'type',


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


}
