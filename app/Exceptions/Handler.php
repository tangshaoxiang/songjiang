<?php

namespace App\Exceptions;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function handler($request, Exception $exception)
    {
        // 只处理自定义的APIException异常
        if($exception instanceof ApiHandler) {
            $errors = $exception->getMessage();
            $errors = is_array($errors)?$errors:[$errors];
            $result = [
                'status'   => false,
                'code'     => $exception->getCode(),
                'error'    => array_shift($errors),
            ];
            return response()->json($result);
        }
        return parent::render($request, $exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
//        return parent::render($request, $exception);

        return $this->handler($request, $exception);//修改后的
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        $errors = [];
        foreach ($exception->errors() as $key => $value){
            $errors[] = $key.':'.implode('|', $value);
        }
        return response()->json([
            'status' => false,
            'code'   => $exception->status,
            'msg'    => $exception->getMessage(),
            'error'  => array_shift($errors),
        ]);
    }
}
