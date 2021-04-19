<?php

namespace App\Model\Recipe;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model{
    protected $table = 'recipe';

    public function ingredient(){
        $table = __CLASS__ . 'Ingredient';
        return $this->hasMany($table);
    }

    public function step(){
        $table = __CLASS__ . 'Step';
        return $this->hasMany($table);
    }

    public function valuation(){
        $table = __CLASS__ . 'Valuation';
        return $this->hasMany($table);
    }
}
