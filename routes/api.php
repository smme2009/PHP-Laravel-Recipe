<?php

use Illuminate\Http\Request;

Route::group(['middleware' => 'api', 'namespace' => 'Api'], function($router){
    Route::group(['namespace' => 'User'], function($router){
        Route::post('register', 'Register@registerUser');
        Route::post('login', 'Auth@login');

        Route::group(['middleware' => 'auth:api'], function($router){
            Route::post('logout', 'Auth@logout');
            Route::post('userinfo', 'Auth@getUserInfo');
        });
    });
});