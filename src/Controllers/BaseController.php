<?php

namespace Smarttech\Prod\Controllers;

use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => '1',
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendResponsePagination($result, $message, $offset)
    {
        $response = [
            'success' => '1',
            'data' => $result,
            'offset' => $offset,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => '0',
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function tokenError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => '4',
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
