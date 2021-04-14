<?php

namespace App\Model\Recipe;

use Illuminate\Database\Eloquent\Model;

class RecipeStep extends Model{
    protected $table = 'recipe_step';

    protected $fillable = [
        'step',
        'image',
        'description',
    ];
}
