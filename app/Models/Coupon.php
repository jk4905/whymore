<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Coupon extends Base
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_coupons');
    }

    /**
     * 某一用户的优惠券
     *
     * @return mixed
     */
    public static function list(User $user, $amount = 0)
    {
        $list = $user->with(['coupons' => function ($query) {
            return $query->orderBy('status', 'asc');
        }])->where('id', $user->id)->get()->pluck('coupons')[0];
        $list = $list->each(function ($item, $key) use ($amount) {
            $item->status = $item->pivot->status;
            $item->begin_at = date('Y-m-d H:i:s', $item->pivot->begin_at);
            $item->end_at = date('Y-m-d H:i:s', $item->pivot->end_at);
            if (empty($amount)) {
                $item->usable = true;
            } else {
                $item->usable = (bccomp($amount, $item->condition) > 0) ? false : true;
            }
            unset($item->pivot);
        });
        return $list;
    }

    /**
     * 可用优惠券
     *
     * @param User $user
     * @param $amount
     * @return mixed
     */
    public static function usableList(User $user, $amount)
    {
        return $user->with(['coupons' => function ($query) use ($amount) {
            return $query->where('condition', '<=', $amount);
        }])->where('id', $user->id)->get()->pluck('coupons')[0];
    }

    /**
     * 判断优惠券是否可用
     *
     * @param $couponId
     * @param $amount
     * @return \Illuminate\Database\Eloquent\Collection|Model
     */
    public static function checkAvailable($couponId, $amount)
    {
        $user = Auth::user();
        $couponInfo = self::with(['users' => function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('begin_at', '>=', Carbon::now())->where('end_at', '<=', Carbon::now());
        }])->where('condition', '<', $amount)->find($couponId);
        return $couponInfo;
    }

    /**
     * 格式化折扣金额
     *
     * @return string
     */
    public function getDiscountAttribute()
    {
        return number_format($this->attributes['discount'], 1, '.', '');
    }

    /**
     * 格式化条件金额
     *
     * @return string
     */
    public function getConditionAttribute()
    {
        return number_format($this->attributes['condition'], 1, '.', '');
    }
}
