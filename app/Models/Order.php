<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'order_number', 'status', 'subtotal', 'shipping', 'total'];
}
