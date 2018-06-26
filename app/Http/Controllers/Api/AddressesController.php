<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use App\Models\Area;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class AddressesController extends Controller
{
    public function __construct()
    {

    }

    /**
     * 获取省份列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProvinces()
    {
        return $this->success(['list' => Province::all()]);
    }

    /**
     * 获取城市列表
     *
     * @param City $city
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(City $city)
    {
        return $this->success(['list' => $city->where('province_id', $city->id)->get()]);
    }

    /**
     * 获取地区列表
     *
     * @param Area $area
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAreas(Area $area)
    {
        return $this->success(['list' => $area->where('city_id', $area->id)->get()]);
    }

    /**
     * 我的地址
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $list = Address::where('user_id', Auth::user()->id)->orderBy('is_default')->get();
        return $this->success(compact('list'));
    }

    /**
     * 添加地址
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:10',
            'mobile' => 'required|numeric|digits:11',
            'province_id' => 'required|numeric|exists:provinces,id',
            'province_name' => 'required|exists:provinces,name',
            'city_id' => 'required|numeric|exists:cities,id',
            'city_name' => 'required|exists:cities,name',
            'area_id' => 'required|numeric|exists:areas,id',
            'area_name' => 'required|exists:areas,name',
            'detailed_address' => 'required|min:1|max:30',
        ]);
        if ($validator->fails()) {
            return $this->fail(40002, $validator->errors());
        }
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $address = Address::create($input);
        return $this->success([]);
    }

    /**
     * 更新地址
     *
     * @param Request $request
     * @param Address $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Address $address)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'filled|string|min:1|max:10',
            'mobile' => 'filled|numeric|digits:11',
            'province_id' => 'filled|numeric|exists:provinces,id',
            'province_name' => 'filled|exists:provinces,name',
            'city_id' => 'filled|numeric|exists:cities,id',
            'city_name' => 'filled|exists:cities,name',
            'area_id' => 'filled|numeric|exists:areas,id',
            'area_name' => 'filled|exists:areas,name',
            'detailed_address' => 'filled|min:1|max:30',
            'is_default' => 'filled|in:1,2',
        ]);
        if ($validator->fails()) {
            return $this->fail(40002, $validator->errors());
        }

//        执行策略
        if (!(Auth::user()->can('update', $address))) {
            return $this->fail(40006);
        }

        foreach ($request->all() as $k => $v) {
            $address->$k = $v;
        }

        $address->save();
        return $this->success([]);
    }

}
