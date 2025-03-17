<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = [
        'name',
    ];

    public function productTypes()
    {
        return $this->hasMany(ProductType::class);
    }
}
