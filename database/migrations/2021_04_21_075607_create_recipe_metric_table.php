<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipeMetricTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('recipe_metric', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('recipe_id');
            $table->bigInteger('user_id');
            $table->timestamps();

            $table->index('recipe_id');
            $table->index('user_id');
            $table->unique(['recipe_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('recipe_metric');
    }
}
