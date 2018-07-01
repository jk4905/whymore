<?php

namespace app\Http\Controllers\Api;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use app\Models\User;
use Auth;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\Exception;

class UsersController extends Controller
{
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::attempt(['mobile' => request('mobile'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken(env('APP_NAME'))->accessToken;
            return $this->success($success);
        } else {
            return $this->fail(40001);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:11|unique:mysql.users,mobile',
            'code' => 'required|digits:6',
            'password' => 'required|min:6|max:32',
        ], [
            'unique' => '您已经注册过本网站了'
        ]);
        if ($validator->fails()) {
            return $this->fail(40002, $validator->errors());
        }

        $redisCode = Redis::get('mobileCode' . $request->mobile);
        if ($redisCode != $request->code) {
            return $this->fail(40004);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken(env('APP_NAME'))->accessToken;
        $success['mobile'] = $user->mobile;
        Redis::del('mobileCode' . $request->mobile);
        Auth::login($user);
        return $this->success($success);
    }

    /**
     * @param $mobile
     * @throws \Overtrue\EasySms\Exceptions\InvalidArgumentException
     * @throws \Overtrue\EasySms\Exceptions\NoGatewayAvailableException
     */
    public function sendSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:11|unique:mysql.users,mobile'
        ], [
            'unique' => '您已经注册过本网站了'
        ]);
        if ($validator->fails()) {
            return $this->fail(40002, $validator->errors()->toArray());
        }
        $code = rand(100000, 999999);
        // 时区很重要，服务器时区要一致
        try {
//            dd(date('Y-m-d H:i:s', time()));
            $config = config('sms');
            $easySms = new EasySms($config);
            $ret = $easySms->send($request->mobile, [
                'template' => 'SMS_75930058',
                'data' => [
                    // 这个键值是阿里云中定义的
                    'number' => $code
                ],
            ]);
            if ($ret['aliyun']['status'] == 'success' && $ret['aliyun']['result']['Code'] == 'OK') {
                Redis::setex('mobileCode' . $request->mobile, 300, $code);
                return $this->success([]);
            } else {
                return $this->fail(40003);
            }
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
//            dd($exception->getExceptions());
            $this->fail(40003);
        }
    }

    /**
     * 更新个人资料
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
//        验证
        $validator = Validator::make($request->all(), [
            'name' => 'min:2|max:16|filled',
            'avatar' => 'string|filled'
        ]);
        if ($validator->fails()) {
            return $this->fail(40002, $validator->errors()->toArray());
        }
//        获得用户实例
        $user = Auth::user();
        foreach ($request->all() as $key => $item) {
            if (empty($item)) {
                continue;
            }
            $user->$key = $item;
        }
//        保存
        $user->save();
//        返回更新后的实例
        if (!empty($user->avatar)) {
            $disk = Storage::disk('qiniu');
            $user->avatar = $disk->getUrl($user->avatar);
        }
        return $this->success($user);
    }

    /**
     * 上传图片
     * @param Request $request
     */
    public function uploadAvatar(Request $request, User $user)
    {
        dd($request->all());
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|file',
        ]);
        if ($validator->fails()) {
            return $this->fail(40002, $validator->errors()->toArray());
        }
        $path = $request->file('avatar')->store('/');
        $realPath = public_path('upload/') . $path;
        $disk = Storage::disk('qiniu');
        $ret = $disk->put($path, file_get_contents($realPath));
        if (!$ret) {
            return $this->fail(40005);
        }
        $url = $disk->getUrl($path);
//        删除本地文件
        unlink($realPath);
        return $this->success(['avatar' => $path, 'preview' => $url]);
    }
}
