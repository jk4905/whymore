<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
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
            return $this->fail(400001);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:11',
            'code' => 'required|digits:6',
            'password' => 'required|min:6|max:32',
        ]);
        if ($validator->fails()) {
            return $this->fail(400002, $validator->errors());
        }

        $sessionMobile = session('mobile');
        $sessionCode = session('code');
        if ($request->mobile != $sessionMobile || $request->code != $sessionCode) {
            $this->fail(400004);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
//        dd($input);
        $user = User::create($input);
        $success['token'] = $user->createToken(env('APP_NAME'))->accessToken;
        $success['mobile'] = $user->mobile;
        session()->remove('code');
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
        ]);
        if ($validator->fails()) {
            return $this->fail(400002, $validator->errors());
        }
        $code = rand(100000, 999999);
        session(['code' => $code, 'mobile' => $request->mobile]);
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
                return $this->success([]);
            } else {
                return $this->fail(400003);
            }
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            dd($exception->getExceptions());
            $this->fail();
        }
    }
}
