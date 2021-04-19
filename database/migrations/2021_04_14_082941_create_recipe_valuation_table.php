<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipeValuationTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('recipe_valuation', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('recipe_id');
            $table->bigInteger('user_id');
            $table->string('score');
            $table->text('description');
            $table->timestamps();

            $table->index('recipe_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('recipe_valuation');
    }
}
