<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Http\Controllers\Controller;
use Request;
use Response;
use Validator;
use ModelRecope;

class RecipeValuation extends Controller
{
    public function __construct(){

    }

    public function createRecipeValuation($recipeId){
        $model = ModelRecope::find($recipeId);

        if($model == false){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $requestDatas = Request::all();

        $validator = $this->getValidator($requestDatas);

        if($validator->fails() == true){
            $message = implode("\n", $validator->errors()->all());
            return Response::json(['message' => $message], 400);
        }

        $createDatas = [
            'user_id' => auth()->id(),
            'score' => $requestDatas['score'],
            'description' => $requestDatas['description'],
        ];

        $result = $model->valuation()->create($createDatas);

        return Response::json(['message' => '新增留言成功'], 201);
    }

    public function deleteRecipeValuation($recipeId, $valuationId){
        $model = ModelRecope::find($recipeId);

        if($model == false){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $result = $model->valuation()
            ->where('user_id', auth()->id())
            ->where('id', $valuationId)
            ->delete();

        if($result == false){
            return Response::json(['message' => '刪除留言失敗'], 404);
        }

        return Response::json(['message' => '刪除留言成功'], 204);
    }

    public function getRecipeValuation($recipeId){
        $model = ModelRecope::find($recipeId);

        if($model == false){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $valuation = $model->valuation;
        
        $responseDatas = [];
        foreach($valuation as $value){
            $user = $value->user;

            $responseDatas[] = [
                'id' => $value->id,
                'description' => $value->description,
                'user' => [
                    'name' => $user->name,
                    'image' => $user->image
                ],
                'create_time' => $value->created_at,
            ];
        }

        return Response::json($responseDatas, 200);
    }

    private function getValidator($requestDatas){
        $validator = Validator::make($requestDatas, [
            'score' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);
       
        return $validator;
    }
}