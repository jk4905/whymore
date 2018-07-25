<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Models\Address;
use App\Models\Area;
use App\Models\City;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class AddressesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取省份列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProvinces()
    {
        return $this->success(['list' => District::query()->where('pid', 0)->get()]);
    }

    /**
     * 获取城市列表
     *
     * @param City $city
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(District $district)
    {
        return $this->success(['list' => $district->where('pid', $district->id)->get()]);
    }

    /**
     * 获取地区列表
     *
     * @param Area $area
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAreas(District $district)
    {
        return $this->success(['list' => $district->where('pid', $district->id)->get()]);
    }

    /**
     * 我的地址
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $list = Address::query()->where('user_id', Auth::user()->id)->orderBy('is_default')->get();
        return $this->success(compact('list'));
    }

    /**
     * 添加地址
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:10',
            'mobile' => 'required|numeric|digits:11',
            'province_id' => 'required|numeric|exists:districts,id',
            'province_name' => 'required|exists:districts,name',
            'city_id' => 'required|numeric|exists:districts,id',
            'city_name' => 'required|exists:districts,name',
            'area_id' => 'required|numeric|exists:districts,id',
            'area_name' => 'required|exists:districts,name',
            'detailed_address' => 'required|min:1|max:30',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        Address::create($input);
        return $this->success([]);
    }

    /**
     * 更新地址
     *
     * @param Request $request
     * @param Address $address
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function update(Request $request, Address $address)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'filled|string|min:1|max:10',
            'mobile' => 'filled|numeric|digits:11',
            'province_id' => 'filled|numeric|exists:districts,id',
            'province_name' => 'filled|exists:districts,name',
            'city_id' => 'filled|numeric|exists:districts,id',
            'city_name' => 'filled|exists:districts,name',
            'area_id' => 'filled|numeric|exists:districts,id',
            'area_name' => 'filled|exists:districts,name',
            'detailed_address' => 'filled|min:1|max:30',
            'is_default' => 'filled|in:1,2',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }

//        执行策略
        if (!(Auth::user()->can('update', $address))) {
            throw new InvalidRequestException(40301);
        }

        if (in_array('is_default', array_keys($request->all()))) {
            $this->setIsDefaultToFalse();
            $address = Address::query()->findOrFail($address->id);
        }

        foreach ($request->all() as $k => $v) {
            $address->$k = $v;
        }
//        dd($address);
        $address->save();
        return $this->success([]);
    }


    public function addDistricts(Request $request)
    {
        $path = $request->file('add')->store('/');
        $realPath = public_path('upload/') . $path;
        $content = json_decode(file_get_contents($realPath), true);
        foreach ($content as $item) {
            if ($item['name'] == '--') {
                continue;
            }
            $data[] = [
                'id' => $item['value'],
                'name' => $item['name'],
                'pid' => (isset($item['parent']) ? $item['parent'] : 0)
            ];
        }
        $a = DB::table('districts')->insert($data);
        unlink($realPath);
        dd($a);
        exit;
    }

    /**
     * 删除地址
     *
     * @param Address $address
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Address $address)
    {
        $address->delete();
        return $this->success([]);
    }

    /**
     * 将全部地址设为否
     *
     * @return mixed
     */
    public function setIsDefaultToFalse()
    {
        return Address::query()->whereIsDefault('1')->whereUserId(Auth::user()->id)->update(['is_default' => 2]);
    }

    public function view(Address $address)
    {
        return $this->success($address);
    }
}
