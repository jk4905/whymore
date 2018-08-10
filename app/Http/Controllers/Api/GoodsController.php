<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InvalidRequestException;
use App\Models\Category;
use App\Models\Goods;
use App\Services\CartService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    /**
     * 首页
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'int|min:1'
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }

        $perPage = empty($request->per_page) ? self::PAGINATE : $request->per_page;
        $list = Goods::query()->whereStatus(1)->orderByDesc('sort')->paginate(self::PAGINATE);
        $newList = $this->getNewPage($list, $perPage);
        return $this->success($newList);
    }

    /**
     * 搜索页面
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'string|max:100',
            'per_page' => 'int|min:1'
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }

        $perPage = empty($request->per_page) ? self::PAGINATE : $request->per_page;

        $builder = Goods::query()->where('status', 1);
        if (!empty($request->q)) {
            $like = '%' . $request->q . '%';
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)->orWhere('description', 'like', $like)->orWhere('keyword', 'like', $like);
            });
        }
        $list = $builder->orderByDesc('sort')->paginate($perPage);
        $newList = $this->getNewPage($list, $perPage);
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

    public function getNewPage($list, $perPage)
    {
        $newList = $this->cartService->getQtyAndRowId($list);
        $page = $this->getPaging($newList, $list->total(), $list->currentPage(), $perPage);
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
    public function getPaging($list, $total, $page, $perPage)
    {
        $paginator = new LengthAwarePaginator($list, $total, $perPage, $page
            , [
                'path' => Paginator::resolveCurrentPath(), //生成路径
                'pageName' => 'page',
            ]);
        return $paginator;
    }
}
