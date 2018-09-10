<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Models\Coupon;
use App\Models\Goods;
use App\Models\Order;
use App\Services\CartService;
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
    protected $cartService;
    public $goodsModel;

    public function __construct(CartService $cartService)
    {
        parent::__construct();
        $this->cartService = $cartService;
        $this->cartInstance = Cart::instance(self::$cart);
        $this->disk = Storage::disk('qiniu');
        $this->goodsModel = new Goods();
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
        return $this->success(['list' => $this->cartService->getGoodsList($content)]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goods.*.id' => 'required|numeric|exists:goods,id,status,1',
            'goods.*.qty' => 'required|numeric|min:1',
        ], [
            'exists' => '没有此商品或商品已下架',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }
        // 获取购物车
        $this->cartInstance->restore(Auth::user()->id);
        foreach ($request->goods as $item) {
            $goods = Goods::findOrFail($item['id']);
            // 添加购物车
            $this->cartInstance->add(['id' => $goods->id, 'name' => $goods->name, 'qty' => $item['qty'], 'price' => $goods->sale_price])->associate(Goods::class);
        }
        // 保存购物车
        $this->cartInstance->store(Auth::user()->id);
        return $this->success();
    }

    /**
     * 更新购物车
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'row_id.*' => 'required|numeric',
            'qty' => 'required|numeric|min:1',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }

        $this->cartService->update($request->all());
        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'row_id.*' => 'required',
        ], [
            'exists' => '没有此商品',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }

        $this->delRowInCart($request->row_id);
        return $this->success();
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

    /**
     * 删除
     *
     * @param $rows
     * @return bool
     */
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

    /**
     * 确认订单
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'row_id.*' => 'required',
        ]);
//
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }
//        获取购物车
        try {
            $this->cartInstance->restore(Auth::user()->id);
            $list = [];
            $goodsAmount = 0;
            foreach ($request->row_id as $rowId) {
                $cartsGoods = $this->cartInstance->get($rowId);
                $goodsInfo = Goods::findOrFail($cartsGoods->id);
                $goodsInfo['qty'] = $cartsGoods->qty;
//                $goodsInfo['image'] =  $this->goodsModel->getImageFullUrl($goodsInfo['image']);
                $list[] = $goodsInfo;
                $goodsAmount = bcadd($goodsAmount, bcmul($goodsInfo['sale_price'], $goodsInfo['qty']));
            }
        } catch (\Exception $e) {
            throw $e;
        } finally {
//            保存购物车
            $this->cartInstance->store(Auth::user()->id);
        }

//        优惠券可用数量
        $couponCount = Coupon::list(Auth::user(), $goodsAmount)->count();

        $shippingType = Order::$shippingType;
        $freight = Order::$freight;
        $payType = Order::$paymentType;
        return $this->success(compact('list', 'couponCount', 'shippingType', 'freight', 'payType'));
    }

}
