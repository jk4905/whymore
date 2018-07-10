<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

/**
 * 系统异常
 *
 * Class InternalException
 * @package App\Exceptions
 */
class InternalException extends Exception
{
    protected $msgForUser;

    public function __construct(string $message, string $msgForUser = '系统内部错误', int $code = 500)
    {
        parent::__construct($message, $code);
        $this->msgForUser = $msgForUser;
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
                'message' => $this->msgForUser,
                'data' => $this->message,
            ]);
        }

        return view('pages.error', ['msg' => $this->message]);
    }

}