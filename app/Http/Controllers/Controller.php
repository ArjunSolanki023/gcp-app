<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function sendResponse($result, $message)
    {
        $response = [
            'flag' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    public function sendBadRequest($returnMessage)
    {
        $response = [
            'flag' => false,
            'message' => is_string($returnMessage) ? ucfirst(strtolower($returnMessage)) : $returnMessage
        ];
        return response()->json($response);
    }
}
