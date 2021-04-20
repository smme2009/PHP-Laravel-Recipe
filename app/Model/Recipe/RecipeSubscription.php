<?php

namespace App\Model\Recipe;

use Illuminate\Database\Eloquent\Model;

class RecipeSubscription extends Model{
    protected $table = 'recipe_subscription';

    protected $fillable = [
        'user_id',
    ];
}
