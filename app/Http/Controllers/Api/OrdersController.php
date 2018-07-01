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

class OrdersController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        parent::__construct();
        $this->cartService = $cartService;
    }

    public function index()
    {
        return $this->success(['list' => Order::query()->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get()]);
    }

    public function show(Order $order)
    {
        return $this->success(Order::with('items')->where('user_id', Auth::user()->id)->findOrFail($order->id));
    }

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
            return $this->fail(40002, $validator->errors());
        }

        $cartContent = $this->cartService->getContent();
        if ($cartContent->isEmpty()) {
            return $this->fail(40008);
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


    public function alipay(Order $order)
    {
        $order->checkPay();
        $data = [
            'out_trade_no' => $order->order_id,
            'total_amount' => sprintf("%.2f", $order->real_amount),
            'subject' => env('APP_PAY_NAME'),
        ];

        return app('alipay')->wap($data);
    }


}
