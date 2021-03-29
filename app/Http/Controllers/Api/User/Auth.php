<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Request;
use Response;

class Auth extends Controller
{
    public function __construct(){

    }

    public function login(){
        $requestDatas = Request::all();

        $apiKey = $this->getApiKey($requestDatas);

        if($apiKey == false){
            $responseDatas = [
                'state' => 'fail',
                'message' => '登入失敗',
            ];

            return Response::json($responseDatas, 401);
        }

        $responseDatas = [
            'state' => 'success',
            'message' => '登入成功',
            'api_key' => $apiKey,
        ];

        return Response::json($responseDatas, 200);
    }

    public function logout(){
        auth()->logout();
        
        $responseDatas = [
            'state' => 'success',
            'message' => '登出成功',
        ];

        return Response::json($responseDatas, 200);
    }

    public function getUserInfo(){
        $userInfo = auth()->user();
        
        unset($userInfo->id);
        unset($userInfo->created_at);
        unset($userInfo->updated_at);

        return Response::json($userInfo, 200);
    }

    private function getApiKey($requestDatas){
        $apiKey = auth()->attempt([
            'email' => $requestDatas['email'],
            'password' => $requestDatas['password'],
        ]);

        return $apiKey;
    }
}
