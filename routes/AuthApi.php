<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['api', 'jwt.auth'], 'namespace' => 'Api'], function($router){
    Route::group(['namespace' => 'User'], function($router){
            Route::post('logout', 'Auth@logout');
            Route::post('userinfo', 'Auth@getUserInfo');
    });

    Route::group(['namespace' => 'Recipe'], function($router){
        Route::group(['prefix' => 'user'], function($router){
            Route::get('recipes', 'Recipe@getUserRecipeList');
            Route::get('subscribes', 'RecipeSubscription@getUserRecipeSubscriptionList');
        });
        
        Route::group(['prefix' => 'recipes'], function($router){
            Route::post('', 'Recipe@createRecipe');
            Route::put('{recipeId}', 'Recipe@updateRecipe')->where('recipeId', '[0-9]+');
            Route::delete('{recipeId}', 'Recipe@deleteRecipe')->where('recipeId', '[0-9]+');
            
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