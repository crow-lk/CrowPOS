<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    protected $fillable = [
        'name',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id'); // Assuming 'productType_id' is the foreign key
    }
}
