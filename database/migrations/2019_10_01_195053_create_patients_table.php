<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->references('id')->on('users');  
            $table->unsignedBigInteger('doctor_id')->references('id')->on('users')->nullable();  
            $table->unsignedBigInteger('gender_id')->references('id')->on('genders');  
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->dateTime('birthday')->nullable();
            $table->string('home_address')->nullable();
            $table->string('phone')->nullable();
            $table->string('comments')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('modified_by')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('gender_id')->references('id')->on('genders');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
