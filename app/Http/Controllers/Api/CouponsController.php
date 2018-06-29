<?php

namespace App\Http\Controllers\Api;

use App\Models\Coupon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CouponsController extends Controller
{

    public function index()
    {
        $list = Auth::user()->with(['coupons' => function ($query) {
            return $query->orderBy('status', 'asc');
        }])->where('id', Auth::user()->id)->get();
        $list = $list->pluck('coupons')[0];
        $list = $list->each(function ($item, $key) {
            $item->status = $item->pivot->status;
            $item->begin_at = date('Y-m-d H:i:s', $item->pivot->begin_at);
            $item->end_at = date('Y-m-d H:i:s', $item->pivot->end_at);
            unset($item->pivot);
        });
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
