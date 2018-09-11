<?php

namespace App\Models;

use App\Exceptions\InvalidRequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Base
{
//    快递类型
    public static $shippingKey = [1, 2];
    public static $shippingType = [
        1 => '快递',
        2 => '送货上门'
    ];

//    支付类型
    public static $paymentTypeKey = [1, 2];
    public static $paymentType = [
        1 => '支付宝',
        2 => '微信'
    ];

//    状态，1-未付款，2-已付款，3-未发货，4-已发货，5-交易完成，6-交易关闭
    public static $status = [
        1 => '未付款',
        2 => '已付款',
        3 => '未发货',
        4 => '已发货',
        5 => '交易完成',
        6 => '交易关闭',
    ];

    public static $freight = 10;

    protected $guarded = [

    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id');
    }

    /**
     * 生成订单号
     *
     * @return bool|string
     */
    public static function findAvailableOrderId()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $orderId = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('order_id', $orderId)->exists()) {
                return $orderId;
            }
        }
        \Log::warning('find order no failed');

        return false;
    }

    /**
     * @return bool
     * @throws InvalidRequestException
     */
    public function checkPay()
    {
        if ($this->status != 1) {
            throw new InvalidRequestException('订单状态错误');
        }
        return true;
    }

    /**
     * 验证支付是否有效
     *
     * @param $amount
     * @throws InvalidRequestException
     */
    public function checkPaymentValid($amount)
    {
        if ($this->real_amount != $amount) {
            throw new InvalidRequestException(40010);
        }
        return true;
    }
}
