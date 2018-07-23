<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use App\Models\Goods;
use App\Services\CartService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoodsController extends Controller
{
    public $cartService;

    public function __construct()
    {
        parent::__construct();
        $this->cartService = new CartService();
    }

    const PAGINATE = 20;

    public function show()
    {
        return $this->success(Goods::all());
    }

    public function index()
    {
        $list = Goods::query()->whereStatus(1)->orderByDesc('sort')->paginate(self::PAGINATE);
        return $this->success(compact('list'));
    }

    public function search(Request $request)
    {
        $builder = Goods::query()->where('status', 1);
        if (!empty($request->q)) {
            $like = '%' . $request->q . '%';
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)->orWhere('description', 'like', $like)->orWhere('keyword', 'like', $like);
            });
        }
        $list = $builder->orderByDesc('sort')->paginate(self::PAGINATE);
        return $this->success(compact('list'));
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
}
