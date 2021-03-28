<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\User;
use Request;
use Validator;
use Response;
use Hash;

class Register extends Controller{
    public function __construct(){

    }

    public function registerUser(){
        $requestData = Request::all();

        $validator = $this->getValidator($requestData);

        if($validator->fails() == true){
            $responseDatas = [
                'state' => 'fail',
                'message' => implode("\n", $validator->errors()->all()),
            ];

            return Response::json($responseDatas, 200);
        }

        $result = $this->createUser($requestData);

        if($result == false){
            $responseDatas = [
                'state' => 'fail',
                'message' => '註冊帳號失敗',
            ];

            return Response::json($responseDatas, 400);
        }

        $responseDatas = [
            'state' => 'success',
            'message' => '註冊帳號成功',
            'api_key' => $this->getApiKey($requestData),
        ];

        return Response::json($responseDatas, 200);
    }

    private function getValidator($requestData){
        $validator = Validator::make($requestData, [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'alpha_num', 'min:8', 'max:20'],
        ]);
       
        return $validator;
    }

    private function createUser($requestData){
        $result = User::create([
            'email' => $requestData['email'],
            'password' => Hash::make($requestData['password']),
        ]);

        return $result; 
    }

    private function getApiKey($requestData){
        $apiKey = auth()->attempt([
            'email' => $requestData['email'],
            'password' => $requestData['password'],
        ]);

        return $apiKey;
    }
}
