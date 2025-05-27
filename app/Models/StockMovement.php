<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'movement_type',
        'supplier_id',
        'products',
        'reason',
        'cost_price',
        'quantity',
        'from_store_id',
        'to_store_id',
    ];

    protected $casts = [
        'products' => 'json',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }
    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }
}


