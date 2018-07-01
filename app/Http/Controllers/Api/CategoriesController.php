<?php

namespace app\Http\Controllers\API;

use app\Models\Category;
use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    public function getFirstCategories()
    {
        $categories = new Category();
        return $this->success($categories->where('pid', 0)->orderBy('sort', 'desc')->get());
    }

    public function test()
    {
        return $this->success(Category::with('childrenCategories')->where('pid', 0)->get());
    }
}
