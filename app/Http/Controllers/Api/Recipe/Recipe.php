<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Http\Controllers\Controller;
use Request;
use Response;
use Validator;
use DB;
use ModelRecope;
use Storage;

class Recipe extends Controller
{
    public function __construct(){

    }

    public function getRecipeList(){
        $responseDatas = ModelRecope::with(['ingredient', 'step'])->get();

        return Response::json($responseDatas, 200);
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

        $stepDatas = $model->step;
        foreach($stepDatas as $key => $value){
            $stepDatas[$key]->image = Storage::url($value->image);
        }

        $responseDatas = [
            'id' => $recipeId,
            'name' => $model->name,
            'star' => $model->star,
            'description' => $model->description,
            'image' => Storage::url($model->image),
            'create_time' => $model->created_at,
            'update_time' => $model->updated_at,
            'ingredients' => $model->ingredient,
            'steps' => $stepDatas,
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
            'star' => ['required'],
            'ingredients' => ['required', 'array'],
            'image' => ['required'],
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
            $model->star = $requestDatas['star'];
            $model->image = $this->setFile($requestDatas['image']);
            $model->save();

            foreach($requestDatas['steps'] as $key => $value){
                $requestDatas['steps'][$key]['image'] = $this->setFile($value['image']);
            }

            $model->ingredient()->createMany($requestDatas['ingredients']);
            $model->step()->createMany($requestDatas['steps']);

            DB::commit();
            return $model->id;
        }catch(Exception $e){
            DB::rollback();
            return false;
        }
    }

    private function setFile($file){
        $fileName = Storage::disk('public')->put(null, $file);
        return $fileName;
    }
}