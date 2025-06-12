<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'avatar',
        'user_id',
        'store_id'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function getAvatarUrl()
    {
        return Storage::url($this->avatar);
    }
}
