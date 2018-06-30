<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Libraries\Carts;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
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
    public function __construct()
    {
        parent::__construct();
        $this->cart = new Carts();
    }

    public function index()
    {
        return $this->success(['list' => Order::query()->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get()]);
    }

    public function show(Order $order)
    {
        return $this->success(Order::with('items')->where('user_id', Auth::user()->id)->findOrFail($order->id));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
//            'mobile' => 'required|digits:11|unique:mysql.users,mobile',
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

        $cartContent = $this->cart->getContent();
        $cartContent = $cartContent->whereIn('rowId', $request->row_id);

//        计算总金额
        $amount = $cartContent->sum(function ($item) {
            return bcmul($item->qty, $item->model->sale_price);
        });

//        计算实际支付金额和折扣
        list($realAmount, $discount) = $this->getRealAmountAndDiscount($amount, $request->coupon_id);

//        地址
        $address = Address::findOrFail($request->address_id);

        $order = DB::transaction(function () use ($user, $request, $amount, $discount, $realAmount, $address, $cartContent) {
            $orderId = Order::findAvailableOrderId();
            $order = new Order([
                'order_id' => $orderId,
                'total_amount' => $amount,
                'discount' => $discount,
                'real_amount' => $realAmount,
                'freight' => Order::$freight,
                'payment_type' => $request->payment_type,
                'name' => $address->name,
                'mobile' => $address->mobile,
                'address' => $address->getFullAddress(),
                'remark' => $request->remark,
                'shipping_type' => $request->shipping_type,
                'coupon_id' => $request->coupon_id ?: 0,
            ]);
            $order->user()->associate($user);

            $goodsCount = $cartContent->count();
            $itemDiscount = 0;
            if (!empty($discount)) {
                $itemDiscount = bcdiv($discount, $goodsCount);
            }

//            item
            $cartContent->each(function ($item, $key) use ($order, $itemDiscount) {
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'goods_id' => $item->id,
                    'goods_name' => $item->name,
                    'qty' => $item->qty,
                    'description' => $item->model->description,
                    'image' => getImgUrl($item->model->image),
                    'price' => $item->model->sale_price,
                    'discount' => $itemDiscount,
                    'item_amount' => bcsub(bcmul($item->model->sale_price, $item->qty), $itemDiscount),
                ]);
                $item->order_id = $order->order_id;
                $item->save();
            });

            $order->save();
//            return $order;
        });

//        删除购物车商品
        $this->cart->remove($request->row_id);

        return $this->success(['redirect_url' => route('alipay', ['id' => $order->id])]);
    }

    /**
     * 获取付款金额和折扣
     *
     * @param $amount
     * @param $couponId
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getRealAmountAndDiscount($amount, $couponId)
    {
        $discount = 0;
        if ($couponId) {
            $couponInfo = Coupon::checkAvailable($couponId, $amount);
            if (empty($couponInfo)) {
                return $this->fail(40007);
            }
            switch ($couponInfo->type) {
                case 1:
                    $discount = $couponInfo->discount;
                    break;
                case 2:
                    $discount = bcmul($amount, $couponInfo->discount);
                    break;
            }
            $realAmount = bcadd(bcsub($amount, $discount), Order::$freight);
        } else {
            $realAmount = bcadd($amount, Order::$freight);
        }

        return [$realAmount, $discount];
    }

    public function alipay(Order $order)
    {
        $order->checkPay();

        $data = [
            'out_trade_no' => $order->order_id,
            'total_amount' => sprintf("%.2f", $order->real_amount),
            'subject' => 'test subject - 测试',
        ];

        return app('alipay')->wap($data);
    }


}
