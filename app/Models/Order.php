<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'store_id',

    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function getCustomerName()
    {
        if ($this->customer) {
            return $this->customer->first_name . ' ' . $this->customer->last_name;
        }
        return __('customer.working');
    }

    public function total()
    {
        // Calculate total price of items
        $totalPrice = $this->items->map(function ($item) {
            return $item->price;
        })->sum();

        // Calculate total discount
        $totalDiscount = $this->items->map(function ($item) {
            return $item->discount;
        })->sum();

        // Return total after discount
        return $totalPrice - $totalDiscount;
    }

    public function formattedTotal()
    {
        return number_format($this->total(), 2);
    }

    public function totalAmount()
    {
        return $this->items->map(function ($i) {
            return $i->price;
        })->sum();
    }

    public function formattedTotalAmount()
    {
        return number_format($this->totalAmount(), 2);
    }

    public function discount()
    {
        return $this->items->map(function ($item) {
            return $item->discount;
        })->sum();
    }

    public function formattedDiscount()
    {
        return number_format($this->discount(), 2);
    }

    public function receivedAmount()
    {
        return $this->payments->map(function ($payment) {
            return $payment->amount;
        })->sum();
    }

    public function formattedReceivedAmount()
    {
        return number_format($this->receivedAmount(), 2);
    }
}
