<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function getUsableCoupon(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'amount' => "required|numeric|min:0",
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }

//        $list = Coupon::usableList($user, $order->real_amount);
        $list = Coupon::list($user, $request->amount);
        return $this->success(compact('list'));
    }
}
