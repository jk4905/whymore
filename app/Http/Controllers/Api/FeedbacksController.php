<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeedbacksController extends Controller
{
    const PAGINATE = 20;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'int|min:1'
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }

        $perPage = empty($request->per_page) ? self::PAGINATE : $request->per_page;

        $list = Feedback::query()->where('is_deleted', 0)->orderByDesc('id')->paginate($perPage);
        return $this->success($list);
    }

    /**
     * 详情
     *
     * @param Feedback $feedback
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function show(Feedback $feedback)
    {
        if ($feedback->is_deleted == 1) {
            throw new InvalidRequestException(40020);
        }
        return $this->success($feedback);
    }

    /**
     * 保存
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:500',
            'image' => 'filled|string|max:2048'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException(40002, $this->errorMsg($validator->errors()->messages()));
        }
        $input = $request->all();
        if (Auth::guard('api')->user()) {
            $input['user_id'] = Auth::guard('api')->user()->id;
        }
        Feedback::create($request->all());

        return $this->success([]);
    }
}
