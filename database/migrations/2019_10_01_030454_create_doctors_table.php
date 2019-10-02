<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('points_level_id')->references('id')->on('points_levels');
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->dateTime('birthday')->nullable();
            $table->string('home_address')->nullable();
            $table->integer('acumulated_points')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('points_level_id')->references('id')->on('points_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctors');
    }
}
