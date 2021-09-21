<?php

use Illuminate\Http\Request;

Route::group(['middleware' => 'api', 'namespace' => 'Api'], function($router){
    Route::group(['namespace' => 'User'], function($router){
        Route::post('register', 'Register@registerUser');
        Route::post('login', 'Auth@login');
    });

    Route::group(['namespace' => 'Recipe', 'prefix' => 'recipes'], function($router){
        Route::get('', 'Recipe@getRecipeList');
        Route::get('{recipeId}', 'Recipe@getRecipe')->where('recipeId', '[0-9]+');
    });
});