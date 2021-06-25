<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $with = [
        'customer',
        'shipping_address',
        'billing_address',
        'order_items',
    ];

    public function setApproved() 
    {
        $this->update(['type' => 'approved']);
        return $this;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function billing_address()
    {
        return $this->hasOne(Address::class, 'id', 'billing_address_id');
    }

    public function shipping_address()
    {
        return $this->hasOne(Address::class, 'id', 'shipping_address_id');
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
