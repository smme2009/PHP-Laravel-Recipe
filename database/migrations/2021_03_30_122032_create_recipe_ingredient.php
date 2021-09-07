<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipeIngredient extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('recipe_ingredient', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('recipe_id');
            $table->string('name');
            $table->integer('quantity');
            $table->string('unit');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('recipe_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('recipe_ingredient');
    }
}
