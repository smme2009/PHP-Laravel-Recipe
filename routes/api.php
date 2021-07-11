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
        Route::group(['namespace' => 'Recipe', 'prefix' => 'recipes'], function($router){
            Route::get('', 'Recipe@getRecipeList');
            Route::post('', 'Recipe@createRecipe');
            Route::put('{recipeId}', 'Recipe@updateRecipe')->where('recipeId', '[0-9]+');
            Route::delete('{recipeId}', 'Recipe@deleteRecipe')->where('recipeId', '[0-9]+');
            Route::get('{recipeId}', 'Recipe@getRecipe')->where('recipeId', '[0-9]+');
            
            Route::get('search', 'RecipeSearch@searchRecipe');

            Route::group(['prefix' => 'valuation/{recipeId}'], function($router){
                Route::post('', 'RecipeValuation@createRecipeValuation');
                Route::delete('{valuationId}', 'RecipeValuation@deleteRecipeValuation');
                Route::get('', 'RecipeValuation@getRecipeValuation');
            });

            Route::group(['prefix' => 'subscription/{recipeId}'], function($router){
                Route::post('', 'RecipeSubscription@createRecipeSubscription');
                Route::delete('', 'RecipeSubscription@deleteRecipeSubscription');
            });
            
            Route::group(['prefix' => 'metric/{recipeId}'], function($router){
                Route::post('', 'RecipeMetric@createRecipeMetric');
                Route::delete('', 'RecipeMetric@deleteRecipeMetric');
            });
        });
    });
});