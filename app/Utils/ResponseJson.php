<?php

namespace App\Utils;

class ResponseJson
{
    /**
     * Returns a JSON response with a 403 Forbidden status code.
     *
     * @param string $message The error message to include in the response.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public static function forbidden(string $message = 'Forbidden :D'): \Illuminate\Http\JsonResponse
    {
        return self::default($message, 403);
    }

    /**
     * Returns a JSON response with a not found message.
     *
     * @param string $message The error message. Defaults to 'Not Found :('.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public static function notFound(string $message = 'Not Found :('): \Illuminate\Http\JsonResponse
    {
        return self::default($message, 404);
    }

    /**
     * Returns a JSON response with a 422 status code and an error message.
     *
     * @param string $message The error message to include in the response.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public static function unprocessable(string $message = 'Unprocessable content :( It\'s all correct?'): \Illuminate\Http\JsonResponse
    {
        return self::default($message, 422);
    }

    /**
     * Returns a JSON response with a message and status code.
     *
     * @param string $message The message to include in the response.
     * @param int $code The status code for the response.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public static function default(string $message, int $code): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => $message], $code);
    }
}
