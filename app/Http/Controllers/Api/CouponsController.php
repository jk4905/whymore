<?php

namespace app\Http\Controllers\Api;

use app\Models\Coupon;
use app\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use app\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CouponsController extends Controller
{

    public function index()
    {
        $list = Coupon::list(Auth::user());
        return $this->success(compact('list'));
    }

    public function add(Coupon $coupon)
    {
        $user = Auth::user();
        $beginAt = strtotime(date('Y-m-d', time()));
        $userCouponData = [
            'begin_at' => $beginAt,
            'end_at' => $beginAt + $coupon->expires * 86400,
            'status' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        $user->coupons()->attach($coupon->id, $userCouponData);
        return $this->success([]);
    }

    public function change(Coupon $coupon, $status)
    {
        return Auth::user()->coupons()->updateExistingPivot($coupon->id, ['status' => $status]);
    }
}
