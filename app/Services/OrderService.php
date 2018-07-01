<?php

namespace app\Services;

use app\Exceptions\InvalidRequestException;
use app\Models\Address;
use app\Models\Coupon;
use app\Models\Order;
use app\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function store(User $user, Address $address, $input, $cartContent)
    {

        //        计算总金额
        $amount = $cartContent->sum(function ($item) {
            return bcmul($item->qty, $item->model->sale_price);
        });

        list($realAmount, $discount) = $this->getRealAmountAndDiscount($amount, $input['coupon_id']);

//        计算实际支付金额和折扣

        $order = DB::transaction(function () use ($user, $input, $amount, $discount, $realAmount, $address, $cartContent) {
            $orderId = Order::findAvailableOrderId();
            $order = new Order([
                'order_id' => $orderId,
                'total_amount' => $amount,
                'discount' => $discount,
                'real_amount' => $realAmount,
                'freight' => Order::$freight,
                'payment_type' => $input['payment_type'],
                'name' => $address->name,
                'mobile' => $address->mobile,
                'address' => $address->getFullAddress(),
                'remark' => $input['remark'],
                'shipping_type' => $input['shipping_type'],
                'coupon_id' => $input['coupon_id'] ?: 0,
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
            return $order;
        });

        return $order;
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
                throw new InvalidRequestException(407, 407);
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

}