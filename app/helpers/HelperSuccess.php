
@php

if (!function_exists('apiResponse')) {
    /**
     * Return a standardized API response.
     *
     * @param bool $success
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    function apiResponse($success, $message, $data = null, $status = 200)
    {
        return response()->json([
            'success' => $success,
            'msg' => trans($message),
            'data' => $data,
        ], $status);
    }
}
