<?php

namespace App\Model\Recipe;

use Illuminate\Database\Eloquent\Model;

class RecipeMetric extends Model{
    protected $table = 'recipe_metric';

    protected $fillable = [
        'user_id',
    ];
}
