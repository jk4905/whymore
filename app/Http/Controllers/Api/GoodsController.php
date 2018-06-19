<?php

namespace App\Http\Controllers\API;

use App\Models\Goods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoodsController extends Controller
{
    public function show()
    {
        return $this->success(Goods::all());
    }
}
