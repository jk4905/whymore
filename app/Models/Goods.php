<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Goods extends Base
{
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'id', 'category_id');
    }

}
