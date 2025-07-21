<?php

namespace App\Exceptions;

use HttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;


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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */

    public function register()
    {

        // $this->renderable(function (Throwable $e, $request) {
        //     if ($request->is('api/*')) {
        //         $status_code = 200;
        //         if ($e instanceof AuthenticationException && $e->getMessage() == 'Unauthenticated.') {
        //            $status_code = 401;
        //         }

        //         return response()->json([
        //             'errorMessage' => $e->getMessage(),
        //             'isSuccessful' => false,
        //         ], $status_code);
        //     }
        // });

        // $this->reportable(function (Throwable $e){
        // });


    }

}
