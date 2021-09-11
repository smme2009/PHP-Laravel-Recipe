<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Http\Controllers\Controller;
use Request;
use Response;
use Validator;
use DB;

class RecipeSearch extends Controller
{
    private $sortTable = [
        'message' => 'recipe_valuation',
        'subscriptions' => 'recipe_subscription',
    ];

    public function __construct(){

    }

    public function searchRecipe(){
        $requestDatas = Request::all();

        $validator = $this->getValidator($requestDatas);

        if($validator->fails() == true){
            $message = implode("\n", $validator->errors()->all());
            return Response::json(['message' => $message], 400);
        }

        $sortTable = $this->sortTable[$requestDatas['sort_type']];

        $recipeDatas = DB::table('recipe AS a')
            ->select('a.*')
            ->leftJoin($sortTable . ' AS b', 'a.id', '=', 'b.recipe_id')
            ->where('a.name', 'like', '%' . $requestDatas['search_text'] . '%')
            ->groupBy('a.id')
            ->orderBy(DB::raw('COUNT(a.id)'), 'asc')
            ->skip($requestDatas['page_size'] * ($requestDatas['current_page'] - 1))
            ->take($requestDatas['page_size'])
            ->get();

        $userIds = $recipeDatas->pluck('user_id')->all();
        $recipeIds = $recipeDatas->pluck('id')->all();

        $userDatas = DB::table('users')
            ->select('id', 'name')
            ->whereIn('id', $userIds)
            ->get();
        
        $ingredientDatas = DB::table('recipe_ingredient')
            ->whereIn('recipe_id', $recipeIds)
            ->get();

        $subscriptionDatas = DB::table('recipe_subscription')
            ->select('recipe_id', DB::raw('COUNT(recipe_id) AS total'))
            ->whereIn('recipe_id', $recipeIds)
            ->groupBy('recipe_id')
            ->get();

        $responesDatas = [];
        foreach($recipeDatas as $key => $value){
            $responesDatas[] = [
                'id' => $value->id,
                'name' => $value->name,
                'create_time' => $value->created_at,
                'author' => $userDatas->where('id', $value->user_id),
                'ingredients' => $ingredientDatas->where('recipe_id', $value->id)->pluck('name')->all(),
                'subscriptions' => $subscriptionDatas->where('recipe_id', $value->id)->first()->total ?? 0,
            ];
        }

        return Response::json($responesDatas, 200);
    }

    private function getValidator($requestDatas){
        $validator = Validator::make($requestDatas, [
            'page_size' => ['required', 'integer'],
            'current_page' => ['required', 'integer'],
            'sort_type' => ['required', 'string'],
            'search_text' => ['required', 'string'],
        ]);
       
        return $validator;
    }
}