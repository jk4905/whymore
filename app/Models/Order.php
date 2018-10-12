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

    const STATUS_NOT_PAID    = 1;
    const STATUS_PAID        = 2;
    const STATUS_NOT_SHIPPED = 3;
    const STATUS_SHIPPED     = 4;
    const STATUS_COMPLETE    = 5;
    const STATUS_CLOSE       = 6;

//    状态，1-未付款，2-已付款，3-未发货，4-已发货，5-交易完成，6-交易关闭
    public static $status = [
        self::STATUS_NOT_PAID => '未付款',
        self::STATUS_PAID => '已付款',
        self::STATUS_NOT_SHIPPED => '未发货',
        self::STATUS_SHIPPED => '已发货',
        self::STATUS_COMPLETE => '交易完成',
        self::STATUS_CLOSE => '交易关闭',
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
        if ($this->status != self::STATUS_NOT_PAID) {
            throw new InvalidRequestException(40013);
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
        if (bccomp($this->real_amount, $amount) !== 0) {
            throw new InvalidRequestException(40010);
        }
        if ($this->status != self::STATUS_NOT_PAID) {
            throw new InvalidRequestException(40013);
        }
        return true;
    }

    public function getPayUrl()
    {
        if ($this->status == self::STATUS_NOT_PAID) {
            $this->redirect_url = route('alipay', $this->id);
        } else {
            $this->redirect_url = '';
        }
        return $this;
    }
}
