<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;

abstract class Controller
{
    public function json_response($type, $code, $message, $status, $data = '',$token = '', $indexname = 'user')
	{
		$response['type'] = $type;
		$response['code'] = $code;
		$response['message'] = $message;
		$response['status'] = $status;
		$response['base_url'] = URL::to('/');
		if (!empty($data)) {
			$response['data'] = $data;
		}
		if (!empty($token)) {
			$response['data']['token'] = $token;
		}
		return response()->json($response, $status);
	}
}
