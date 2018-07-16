<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BannersController extends Controller
{
    public function index()
    {
        $list = Banner::query()->where('status', 1)->orderByDesc('sort')->get();
        return $this->success(compact('list'));
    }
}
