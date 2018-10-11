<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Models\RobotConfiguration;
use App\Models\RobotMessage;
use App\Models\RobotMessageUser;
use App\Services\UploadService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RobotsController extends Controller
{

    public function getConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'robot_num' => 'required|numeric|exists:robot_configurations,robot_num,status,1',
        ], [
            'exists' => '后台没有配置此机器人号',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }
        $robotConfiguration = RobotConfiguration::query()->where('robot_num', $request->robot_num)->where('status', 1)->first();
        return $this->success($robotConfiguration);
    }


    /**
     * 保存消息
     *
     * @param Request $request
     * @throws InvalidRequestException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|numeric',
            'to' => 'required|numeric',
            'age' => 'required|integer|min:0',
            'user_name' => 'required|string',
            'avatar' => 'required|string',
            'sex' => 'required|int',
            'send_time' => 'required|date_format:Y-m-d H:i:s',
            'content' => 'required|string|min:1|max:4096',
            'images' => 'required|string|min:1|max:4096',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }
        $input = $request->all();

        $input['type'] = RobotMessage::TYPE_USER;
        $input['qq'] = $input['from'];

//        保存消息
        RobotMessage::create($input);

//        查询是否存在此QQ，没有则更新
        RobotMessageUser::query()->updateOrCreate(['qq' => $input['from']], $input);

        return $this->success([]);
    }

    /**
     * 上传图片
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images.*' => 'required|file',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }
        $images = $request->file('images');

        $paths = [];
        foreach ($images as $image) {
            $paths[] =  UploadService::uploadOne($image);
        }

        return $this->success(compact('paths'));
    }

}
