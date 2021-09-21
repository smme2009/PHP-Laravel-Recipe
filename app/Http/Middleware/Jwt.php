<?php

namespace App\Http\Middleware;

use Closure;
use Jwt as JwtTool;

class Jwt{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $auth = $request->header('Authorization');

        preg_match('/(?i)^Bearer (.*)$/', $auth, $data);
        list($auth, $token) = $data;

        $accountId = JwtTool::getAccountId($token);
        if(!$accountId){
            return Response::json([], 401);
        }

        auth()->loginUsingId($accountId);
        
        return $next($request);
    }
}
