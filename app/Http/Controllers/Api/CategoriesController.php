<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InvalidRequestException;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    /**
     * 根据首页分类获取分类和商品信息
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function getGoodsList(Category $category)
    {
        if ($category->pid != 0) {
            throw new InvalidRequestException(40009);
        }
        $categoryList = Category::with(['goods' => function ($query) {
            $query->where('status', 1)->orderBy('sales', 'desc');
        }])->where('pid', $category->id)->get();
        return $this->success(compact('categoryList'));
    }
}
