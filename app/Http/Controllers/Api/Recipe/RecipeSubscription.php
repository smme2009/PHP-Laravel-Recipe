<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Http\Controllers\Controller;
use App;
use Response;
use ModelRecope;

class RecipeSubscription extends Controller
{
    public function __construct(){

    }

    public function getUserRecipeSubscriptionList(){
        $responseDatas = ModelRecope::with(['ingredient', 'step'])
            ->where('user_id', auth()->id())
            ->get();

        $recipeController = App::make(__NAMESPACE__ . '\Recipe');

        foreach($responseDatas as $key => $value){
            $responseDatas[$key] = $recipeController->setModel($value);
        }
        
        return Response::json($responseDatas, 200);
    }

    public function createRecipeSubscription($recipeId){
        $model = ModelRecope::find($recipeId);

        if($model == false){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $subscription = $model->subscription()
            ->where('user_id', auth()->id())
            ->first();

        if($subscription){
            return Response::json(['message' => '已在訂閱名單'], 400);
        }

        $result = $model->subscription()->create(['user_id' => auth()->id()]);

        if($result == false){
            return Response::json(['message' => '訂閱失敗'], 400);
        }

        return Response::json(['message' => '訂閱成功'], 201);
    }

    public function deleteRecipeSubscription($recipeId){
        $model = ModelRecope::find($recipeId);

        if($model == false){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $result = $model->subscription()
            ->where('user_id', auth()->id())
            ->delete();

        if($result == false){
            return Response::json(['message' => '取消訂閱失敗'], 400);
        }

        return Response::json(['message' => '取消訂閱成功'], 204);
    }
}