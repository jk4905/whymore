<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Base
{
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
