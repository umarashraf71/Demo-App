<?php

namespace App\Traits;

trait HttpResponseTrait
{
    protected function response($data = [], $isSuccessful = true, $message = '', $status = 200)
    {
        return response([
            'isSuccessful' => $isSuccessful,
            'responseMessage' => $message,
            'data' => $data,
        ], $status);
    }

    protected function fail($message = null, $status = 200)
    {
        return response([
            'isSuccessful' => false,
            'responseMessage' => $message,
            'message' => $message,
        ], $status);
    }
}
