<?php

namespace App\Http\Controllers\Api;

use App\Models\Goods;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CartsController extends Controller
{
    protected static $cart = 'shopping_cart';
    public $cartInstance;
    public $disk;

    public function __construct()
    {
        $this->cartInstance = Cart::instance(self::$cart);
        $this->disk = Storage::disk('qiniu');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->cartInstance->restore(Auth::user()->id);
        $this->cartInstance->store(Auth::user()->id);
        $content = $this->cartInstance->content();
        return $this->success(['list' => $this->getGoodsList($content)]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goods_id.*' => 'required|numeric|exists:goods,id,status,1',
//            'qty' => 'required|numeric|min:1',
        ], [
            'exists' => '没有此商品',
        ]);
        if ($validator->fails()) {
            return $this->fail(40002, $validator->errors());
        }
        // 获取购物车
        $this->cartInstance->restore(Auth::user()->id);
        foreach ($request->goods_id as $goodsId) {
            $goods = Goods::findOrFail($goodsId);
            // 添加购物车
            $this->cartInstance->add(['id' => $goods->id, 'name' => $goods->name, 'qty' => 1, 'price' => $goods->sale_price])->associate(Goods::class);
        }
        // 保存购物车
        $this->cartInstance->store(Auth::user()->id);
        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'row_id.*' => 'required',
        ], [
            'exists' => '没有此商品',
        ]);
        if ($validator->fails()) {
            return $this->fail(40002, $validator->errors());
        }

        $this->delRowInCart($request->row_id);
        return $this->success();
    }

    /**
     * 通过购物车获取商品列表
     * @param $content
     * @return array
     */
    public function getGoodsList($content)
    {
        $goodsList = [];
        foreach ($content as $row) {
            $goods = $row->model->toArray();
            if ($goods['status'] == 2) {
                continue;
            }
            $goods['image'] = $this->disk->getUrl($goods['image']);
            $goods['row_id'] = $row->rowId;
            $goods['qty'] = $row->qty;
            $goodsList[] = $goods;
        }
        return $goodsList;
    }

    /**
     * 判断是否在购物车里
     * @param $row
     * @return bool
     */
    public function isInCart($row)
    {
        $cart = $this->cartInstance->content()->toArray();
        $rowIds = array_column($cart, 'rowId');
        return in_array($row, $rowIds);
    }


    public function delRowInCart($rows)
    {
        try {
            // 获取购物车
            $this->cartInstance->restore(Auth::user()->id);
            foreach ($rows as $row) {
                if (!$this->isInCart($row)) {
                    continue;
                }
                // 删除购物车
                $this->cartInstance->remove($row);
            }
            // 保存购物车
            $this->cartInstance->store(Auth::user()->id);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
