<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Http\Controllers\Controller;
use Response;
use ModelRecope;

class RecipeMetric extends Controller{
    public function __construct(){

    }

    public function createRecipeMetric($recipeId){
        $model = ModelRecope::find($recipeId);

        if($model == false){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $metric = $model->metric()
            ->where('user_id', auth()->id())
            ->first();

        if($metric){
            return Response::json(['message' => '已點讚'], 400);
        }

        $result = $model->metric()->create(['user_id' => auth()->id()]);

        if($result == false){
            return Response::json(['message' => '點讚失敗'], 400);
        }

        return Response::json(['message' => '點讚成功'], 201);
    }

    public function deleteRecipeMetric($recipeId){
        $model = ModelRecope::find($recipeId);

        if($model == false){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $result = $model->metric()
            ->where('user_id', auth()->id())
            ->delete();

        if($result == false){
            return Response::json(['message' => '收回讚失敗'], 400);
        }

        return Response::json(['message' => '收回讚成功'], 204);
    }
}