<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipeStep extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('recipe_step', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('recipe_id');
            $table->integer('step');
            $table->text('image');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('recipe_id');
            $table->unique(['recipe_id', 'step']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('recipe_step');
    }
}
