<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Http\Controllers\Controller;
use Request;
use Response;
use Validator;
use DB;
use ModelRecope;
use Storage;
use Str;

class Recipe extends Controller
{
    public function __construct(){

    }

    public function getRecipeList(){
        $responseDatas = ModelRecope::with(['ingredient', 'step'])->get();

        foreach($responseDatas as $key => $value){
            $responseDatas[$key] = $this->setModel($value);
        }

        return Response::json($responseDatas, 200);
    }

    public function getUserRecipeList(){
        $responseDatas = ModelRecope::with(['ingredient', 'step'])
            ->where('user_id', auth()->id())
            ->get();

        foreach($responseDatas as $key => $value){
            $responseDatas[$key] = $this->setModel($value);
        }

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
        $model = ModelRecope::with(['ingredient', 'step'])->find($recipeId);

        if($model == false){
            return Response::json(['message' => '查無此食譜'], 404);
        }

        $model = $this->setModel($model);

        return Response::json($model, 200);
    }

    private function getRequestDatas(){
        $requestDatas = Request::all();
        $requestDatas['ingredient'] = (array)$requestDatas['ingredient'];
        $requestDatas['step'] = (array)$requestDatas['step'];

        return $requestDatas;
    }

    private function getValidator($requestDatas){
        $validator = Validator::make($requestDatas, [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'star' => ['required'],
            'ingredient' => ['required', 'array'],
            'image' => ['required'],
            'step' => ['required', 'array'],
            'ingredient.*.name' => ['required', 'string'],
            'ingredient.*.quantity' => ['required', 'integer'],
            'ingredient.*.unit' => ['required', 'string'],
            'ingredient.*.description' => ['string'],
            'step.*.step' => ['required', 'integer'],
            'step.*.image' => ['required'],
            'step.*.description' => ['required', 'string'],
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
            $model->image = $this->saveBase64File($requestDatas['image']);
            $model->save();

            foreach($requestDatas['step'] as $key => $value){
                $requestDatas['step'][$key]['image'] = $this->saveBase64File($value['image']);
            }

            $model->ingredient()->createMany($requestDatas['ingredient']);
            $model->step()->createMany($requestDatas['step']);

            DB::commit();
            return $model->id;
        }catch(Exception $e){
            DB::rollback();
            return false;
        }
    }

    private function saveBase64File($fullString){
        if(filter_var($fullString, FILTER_VALIDATE_URL)){
            preg_match('/^.*storage\/(.*)$/', $fullString, $data);
            list($fullString, $filePath) = $data;
        }else{
            preg_match('/^data:image\/(.*);base64,(.*)$/', $fullString, $data);
            list($fullString, $extension, $string) = $data;

            $file = base64_decode($string);
            $filePath = Str::random(40) . ".{$extension}";

            Storage::disk('public')->put($filePath, $file);
        }
       
        return $filePath;
    }

    public function setModel($model){
        $model->image = asset(Storage::url($model->image));
        
        foreach($model->step as $key => $value){
            $model->step[$key]->image = asset(Storage::url($value->image));
        }

        return $model;
    }
}