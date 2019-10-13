<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcumulatedPointsLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acumulated_points_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('points_level_id')->references('id')->on('points_levels');
            $table->unsignedBigInteger('user_id')->references('id')->on('users');             
            $table->integer('acumulated_points')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('points_level_id')->references('id')->on('points_levels');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acumulated_points_levels');
    }
}
