<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{
    /**
     * @param $success
     * @param $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function jsonResponse($success, $message, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * @param $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse($data, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $data,
        ];

        return response()->json($response, $statusCode);
    }

    protected function errorResponse($message, int $statusCode = 401): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        return response()->json($response, $statusCode);
    }
}
