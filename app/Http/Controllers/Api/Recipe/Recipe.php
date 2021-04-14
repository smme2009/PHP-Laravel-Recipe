<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Http\Controllers\Controller;
use Request;
use Response;
use Validator;
use DB;
use ModelRecope;

class Recipe extends Controller
{
    public function __construct(){

    }

    public function createRecipe(){
        $requestDatas = $this->getRequestDatas();

        $validator = $this->getValidator($requestDatas);

        if($validator->fails() == true){
            $message = implode("\n", $validator->errors()->all());
            return Response::json(['message' => $message], 400);
        }

        $model = new ModelRecope();

        $result = $this->saveModel($model, $requestDatas);

        if($result == false){
            return Response::json(['message' => '新增失敗'], 400);
        }

        return Response::json(['message' => '新增成功'], 200);
    }

    public function updateRecipe($recipeId){
        $model = ModelRecope::find($recipeId);

        if($model == false || $model->user_id != auth()->id()){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $requestDatas = $this->getRequestDatas();

        $validator = $this->getValidator($requestDatas);

        if($validator->fails() == true){
            $message = implode("\n", $validator->errors()->all());
            return Response::json(['message' => $message], 400);
        }

        $model->ingredient()->delete();
        $model->step()->delete();

        $result = $this->saveModel($model, $requestDatas);

        if($result == false){
            return Response::json(['message' => '編輯失敗'], 400);
        }

        return Response::json(['message' => '編輯成功'], 200);
    }

    public function deleteRecipe($recipeId){
        $model = ModelRecope::find($recipeId);

        if($model == false || $model->user_id != auth()->id()){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $model->ingredient()->delete();
        $model->step()->delete();
        $model->delete();

        return Response::json(['message' => '刪除成功'], 200);
    }

    public function getRecipe($recipeId){
        $model = ModelRecope::find($recipeId);

        if($model == false){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $responseDatas = [
            'id' => $recipeId,
            'name' => $model->name,
            'description' => $model->description,
            'create_time' => $model->created_at,
            'update_time' => $model->updated_at,
            'ingredients' => $model->ingredient,
            'steps' => $model->step,
        ];

        return Response::json($responseDatas, 200);
    }

    private function getRequestDatas(){
        $requestDatas = Request::all();
        $requestDatas['ingredients'] = json_decode($requestDatas['ingredients'] ?? '', true);
        $requestDatas['steps'] = json_decode($requestDatas['steps'] ?? '', true);

        return $requestDatas;
    }

    private function getValidator($requestDatas){
        $validator = Validator::make($requestDatas, [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'send_time' => ['required', 'integer'],
            'ingredients' => ['required', 'array'],
            'steps' => ['required', 'array'],
            'ingredients.*.name' => ['required', 'string'],
            'ingredients.*.quantity' => ['required', 'integer'],
            'ingredients.*.unit' => ['required', 'string'],
            'ingredients.*.description' => ['string'],
            'steps.*.step' => ['required', 'integer'],
            'steps.*.image' => ['required'],
            'steps.*.description' => ['required', 'string'],
        ]);
       
        return $validator;
    }

    private function saveModel($model, $requestDatas){
        try{
            DB::beginTransaction();

            $model->user_id = auth()->id();
            $model->name = $requestDatas['name'];
            $model->description = $requestDatas['description'];
            $model->send_time = $requestDatas['send_time'];

            $model->save();
            $model->ingredient()->createMany($requestDatas['ingredients']);
            $model->step()->createMany($requestDatas['steps']);

            DB::commit();
            return true;
        }catch(Exception $e){
            DB::rollback();
            return false;
        }
    }
}