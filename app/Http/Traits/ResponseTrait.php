<?php

namespace App\Http\Traits;

trait ResponseTrait {

    public function responseSuccess($message, $data = null, $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        return response()->json($response, $code);
    }

    public function responseError($errorMessages = [], $data = null, $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $errorMessages,
            'data' => $data,
        ];

        return response()->json($response, $code);
    }
}


