<?php

namespace App\Http\Middleware;

use Closure;

class Cors{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $cors = $next($request)
            ->header('Access-Control-Allow-Origin', env('FRONTEND_URL'))
            ->header('Access-Control-Allow-Methods', '*')
            ->header('Access-Control-Allow-Headers', 'Origin, Methods, Content-Type, Authorization')
            ->header('Access-Control-Allow-Credentials', true)
            ->header('Access-Control-Max-Age', 3600);

        return $cors;
    }
}
