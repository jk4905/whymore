<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yansongda\Pay\Log;
use Yansongda\Pay\Pay;

class OrdersController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        parent::__construct();
        $this->cartService = $cartService;
    }

    /**
     *  订单列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'int',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }
        $query = Order::with('items');
        if ($request->status > 0) {
            $query->where('status', $request->status);
        }
        $list = $query->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        $list->each(function (&$item) {
            $item->count = $item->items->count();
            $item->images = $item->items->pluck('image');
            if ($item->count > 1) {
                $item->goods_name = '';
            } else {
                $item->goods_name = $item->items->pluck('goods_name')->first();
            }
        });
        return $this->success(compact('list'));
    }

    /**
     * 订单详情
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        return $this->success(Order::with('items')->where('user_id', Auth::user()->id)->findOrFail($order->id));
    }

    /**
     * 保存订单
     *
     * @param Request $request
     * @param OrderService $order
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function store(Request $request, OrderService $order)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:addresses,id,user_id,' . $user->id,
            'row_id.*' => 'required|string',
            'coupon_id' => 'filled|numeric|exists:user_coupons,id,status,1,user_id,' . $user->id,
            'remark' => 'filled|string',
            'payment_type' => [
                'required',
                Rule::in(Order::$paymentTypeKey),
            ],
            'shipping_type' => [
                'required',
                Rule::in(Order::$shippingKey),
            ],
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }

        $cartContent = $this->cartService->getContent();
        if ($cartContent->isEmpty()) {
            throw new InvalidRequestException(40008);
        }
        $cartContent = $cartContent->whereIn('rowId', $request->row_id);

//        地址
        $address = Address::findOrFail($request->address_id);
        $input = $request->all();
//        保存订单
        $order = $order->store($user, $address, $input, $cartContent);

//        删除购物车商品
        $this->cartService->remove($request->row_id);

        return $this->success(['redirect_url' => route('alipay', ['id' => $order->id])]);
    }

    /**
     * 跳转到支付宝
     *
     * @param Order $order
     * @return mixed
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function alipay(Order $order)
    {
//        dd(route('alipay.return'));
        // 判断订单是否属于当前用户
//        $this->authorize('own', $order);

//        $order->checkPay();
//        $data = [
//            'out_trade_no' => $order->order_id,
//            'total_amount' => sprintf("%.2f", $order->real_amount),
//            'subject' => env('APP_PAY_NAME'),
//        ];
//        $data = [
//            'out_trade_no' => date('Y-m-d') . time(),
//            'total_amount' => 0.01,
//            'subject' => env('APP_PAY_NAME'),
//        ];
//        return app('alipay')->web($data);
//        return app('alipay')->wap($data);

        $data = [
            'out_trade_no' => date('Y-m-d') . time(),
            'total_amount' => 0.01,
            'subject' => env('APP_PAY_NAME'),
        ];
        $config = config('pay.alipay');
        dd($config);
        $alipay = Pay::alipay($config)->web($data);

        return $alipay->send();// laravel 框架中请直接 `return $alipay`
    }


    // 前端回调页面
    public function alipayReturn()
    {
        // 校验提交的参数是否合法
        $data = app('alipay')->verify();
        dd($data);
    }

    // 服务器端回调
    public function alipayNotify()
    {
        $config = config('pay.alipay');
        $alipay = Pay::alipay($config);

        try {
            $data = $alipay->verify(); // 是的，验签就这么简单！

            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况

            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            $e->getMessage();
        }

        return $alipay->success();// laravel 框架中请直接 `return $alipay->success()`

        try {
            $data = app('alipay')->verify();
        } catch (\Exception $e) {
            $e->getMessage();
        }
        \Log::debug('Alipay notify', $data->all());
        return app('alipay')->success();
    }
}
