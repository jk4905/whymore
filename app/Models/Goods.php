<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'id', 'category_id');
    }
}
