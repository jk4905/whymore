<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    public function getFirstCategories()
    {
        $categories = new Category();
        return $this->success($categories->where('pid', 0)->orderBy('sort', 'asc')->get());
    }

    public function test()
    {
        return $this->success(Category::with('childrenCategories')->where('pid', 0)->get());
    }
}
