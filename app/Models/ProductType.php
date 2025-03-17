<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductType extends Model
{
    protected $fillable = [
        'name',
        'category_id',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id'); // Assuming 'category_id' is the foreign key
    }

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }
}
