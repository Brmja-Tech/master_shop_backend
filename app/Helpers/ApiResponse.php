<?php

namespace App\Helpers;


class ApiResponse
{
    static function sendResponse($code = 200, $message = null, $data = null, $pagination = null, $summary = null)
    {
        $response = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];

        if ($pagination) {
            $response['pagination'] = $pagination;
        }

        if ($summary) {
            $response['summary'] = $summary;
        }

        return response()->json($response, $code);
    }
}
