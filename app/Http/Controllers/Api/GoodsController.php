<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use App\Models\Goods;
use App\Services\CartService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class GoodsController extends Controller
{
    const PAGINATE = 20;
    public $cartService;

    public function __construct()
    {
        parent::__construct();
        $this->cartService = new CartService();
    }

    public function show()
    {
        return $this->success(Goods::all());
    }

    public function index(Request $request)
    {
        $list = Goods::query()->whereStatus(1)->orderByDesc('sort')->paginate(self::PAGINATE);
        $newList = $this->getNewPage($list);
        return $this->success($newList);
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
        $newList = $this->getNewPage($list);
        return $this->success($newList);
    }

    /**
     * 获取商品详情
     * @param Goods $goods
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoodsDetail(Goods $goods)
    {
        $goods = Goods::query()->where('status', 1)->findOrFail($goods->id);
        $goods = $this->cartService->getQtyAndRowId(collect([$goods]));
        return $this->success($goods);
    }

    public function getNewPage($list)
    {
        $newList = $this->cartService->getQtyAndRowId($list);
        $page = $this->getPaging($newList, $list->total(), $list->currentPage());
        return $page;
    }

    /**
     * 重新分页
     *
     * @param $list
     * @param $total
     * @param $page
     * @return LengthAwarePaginator
     */
    public function getPaging($list, $total, $page)
    {
        $paginator = new LengthAwarePaginator($list, $total, $perPage = self::PAGINATE, $page
            , [
                'path' => Paginator::resolveCurrentPath(), //生成路径
                'pageName' => 'page',
            ]);
        return $paginator;
    }
}
