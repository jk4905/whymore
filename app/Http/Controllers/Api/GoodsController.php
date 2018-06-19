<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use App\Models\Goods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoodsController extends Controller
{
    public function show()
    {
        return $this->success(Goods::all());
    }

    public function getGoodsList(Category $category)
    {
        $categoryList = Category::with(['goods' => function ($query) {
            $query->orderBy('sales', 'desc');
        }])->where('pid', $category->id)->get();
        return $this->success(compact('categoryList'));
    }

    /**
     * 获取商品列表
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoodsListBack1(Category $category)
    {
        $categoryList = Category::with('goods')->where('pid', $category->id)->get();
//        $categoryList = $category->allChildrenCategories;
        return $this->success(compact('categoryList'));
    }

    /**
     * 获取商品详情
     * @param Goods $goods
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoodsDetail(Goods $goods)
    {
        return $this->success($goods);
    }

    public function getGoodsListBack2(Category $category)
    {
        $categoryList = Category::where('pid', $category->id)->get();
//        foreach ($categoryList as $k => $category) {
//            $categoryList[$k] = Category::with(['goods' => function ($query) {
//                $query->where('status', 1)->orderBy('sort', 'desc');
//            }])->where('id', $category->id)->get();
//        }
        $categoryList->transform(function ($item, $key) {
            $item['goodsList'] = Category::with(['goods' => function ($query) {
                $query->where('status', 1)->orderBy('sort', 'desc');
            }])->where('id', $item->id)->get();
            return $item['goodsList'];
        });
        return $this->success($categoryList);
    }
}
