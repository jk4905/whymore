<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    public function category()
    {
        return $this->belongsTo('app\Models\Category', 'id', 'category_id');
    }
}
