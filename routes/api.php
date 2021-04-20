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

    Route::group(['middleware' => 'auth:api'], function($router){
        Route::group(['namespace' => 'Recipe', 'prefix' => 'recipe'], function($router){
            Route::post('', 'Recipe@createRecipe');
            Route::put('{recipeId}', 'Recipe@updateRecipe');
            Route::delete('{recipeId}', 'Recipe@deleteRecipe');
            Route::get('{recipeId}', 'Recipe@getRecipe');

            Route::group(['prefix' => '{recipeId}/valuation'], function($router){
                Route::post('', 'RecipeValuation@createRecipeValuation');
                Route::delete('{valuationId}', 'RecipeValuation@deleteRecipeValuation');
                Route::get('', 'RecipeValuation@getRecipeValuation');
            });

            Route::group(['prefix' => '{recipeId}/subscription'], function($router){
                Route::post('', 'RecipeSubscription@createRecipeSubscription');
                Route::delete('', 'RecipeSubscription@deleteRecipeSubscription');
            });
        });
    });
});