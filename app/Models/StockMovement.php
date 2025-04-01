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
        'items',
        'reason',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}


