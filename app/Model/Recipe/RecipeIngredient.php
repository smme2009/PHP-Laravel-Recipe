<?php

namespace App\Model\Recipe;

use Illuminate\Database\Eloquent\Model;

class RecipeIngredient extends Model{
    protected $table = 'recipe_ingredient';

    protected $fillable = [
        'name',
        'quantity',
        'unit',
        'description',
    ];
}
