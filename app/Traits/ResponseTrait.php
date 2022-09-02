<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    public function successResponse($data, $message = '',$code=200): JsonResponse
    {
        return response()->json([
            'status' => "success",
            'data'    => $data,
            'msg' => $message,
            "code"=>$code
        ],$code);
    }
    public function warningResponse($data, $message = '',$code=200): JsonResponse
    {
        return response()->json([
            'status' => "warning",
            'data'    => $data,
            'msg' => $message,
            "code"=>$code
        ],$code);
    }
    public function errorResponse($message = '', $code = 400): JsonResponse
    {
        return response()->json([
            'data'    => [],
            'msg' => $message,
            "code"=>$code
        ], $code);
    }
}
