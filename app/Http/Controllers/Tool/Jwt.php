<?php

namespace App\Http\Controllers\Tool;

use Firebase\JWT\JWT as FirebaseJwt;

class Jwt
{
    public static function getJwt($data){
        $time = time();

        $payload = [
            'iss' => env('APP_URL'),
            'aud' => env('APP_URL'),
            'iat' => $time,
            'nbf' => $time,
        ];

        $payload = array_merge($payload, $data);

        $token = FirebaseJwt::encode($payload, env('JWT_KEY'));

        return $token;
    }

    public static function getAccountId($token){
        $payload = FirebaseJwt::decode($token, env('JWT_KEY'), ['HS256']);

        $accountId = 0;
        if($payload){
            $limit = $payload->iat + env('JWT_TIME');
            $accountId = ($limit >= time())? $payload->aid: 0;
        }

        return $accountId;
    }
}
