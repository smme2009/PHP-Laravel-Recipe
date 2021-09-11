<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipeTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('recipe', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->string('name');
            $table->text('description');
            $table->string('star');
            $table->text('image');
            $table->timestamps();

            $table->index('user_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('recipe');
    }
}
