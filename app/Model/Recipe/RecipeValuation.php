<?php

namespace App\Model\Recipe;

use Illuminate\Database\Eloquent\Model;

class RecipeValuation extends Model{
    protected $table = 'recipe_valuation';

    protected $fillable = [
        'user_id',
        'score',
        'description',
    ];

    protected $with = [
        'user'
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
