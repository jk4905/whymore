<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        bcscale(4);
    }

    public function success($data = [])
    {
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => config('errorcode.code')[200],
            'data' => $data,
        ]);
    }

    public function fail($code, $msg = '', $data = [])
    {
        $message = '';
        if (empty($msg)) {
            $message = config('errorcode.code')[(int)$code];
        } else {
            if (is_object($msg)) {
                $msg = $msg->toArray();
            }
            $err = array_shift($msg);
            $message = array_shift($err);
//            $message = implode('',1);
        }

        return response()->json([
            'status' => false,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
