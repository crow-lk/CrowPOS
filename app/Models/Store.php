<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    protected $fillable = [
        'name',
        'is_admin',
    ];

    public function stockMovementsTo()
    {
        return $this->hasMany(StockMovement::class, 'to_store_id');
    }
    public function stockMovementsFrom()
    {
        return $this->hasMany(StockMovement::class, 'from_store_id');
    }
}
