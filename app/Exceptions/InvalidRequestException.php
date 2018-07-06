<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

/**
 * 用户行为异常
 *
 * Class InvalidRequestException
 * @package App\Exceptions
 */
class InvalidRequestException extends Exception
{
    public function __construct(int $code = 400, string $message = "")
    {
        parent::__construct($message, $code);
    }

    public function render(Request $request)
    {
        if (empty($this->message)) {
            $this->message = config('errorcode.code')[(int)$this->code];
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => false,
                'code' => $this->code,
                'message' => $this->message,
                'data' => $this->message,
            ]);
        }

        return view('pages.error', ['msg' => $this->message]);
    }
}