<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    protected $fillable = [
        'name',
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'store_id');
    }

    public function stockMovementsTo()
    {
        return $this->hasMany(StockMovement::class, 'to_store_id');
    }
    public function stockMovementsFrom()
    {
        return $this->hasMany(StockMovement::class, 'from_store_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Product::class);
    }
}
