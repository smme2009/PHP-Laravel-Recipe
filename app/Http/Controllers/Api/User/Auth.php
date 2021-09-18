<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Jwt;
use Request;
use Response;

class Auth extends Controller
{
    public function __construct(){

    }

    public function login(){
        $time = time();

        $requestDatas = Request::all();

        $check = $this->checkUser($requestDatas);
        
        if($check == false){
            $responseDatas = [
                'message' => '登入失敗',
            ];

            return Response::json($responseDatas, 401);
        }

        $token = JWT::getJwt(['aid' => auth()->id()]);

        $responseDatas = [
            'message' => '登入成功',
            'api_key' => $token,
        ];

        return Response::json($responseDatas, 200);
    }

    public function logout(){
        auth()->logout();
        
        $responseDatas = [
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

    private function checkUser($requestDatas){
        $check = auth()->attempt([
            'email' => $requestDatas['email'],
            'password' => $requestDatas['password'],
        ]);

        return $check;
    }
}
