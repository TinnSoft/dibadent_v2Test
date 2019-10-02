<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('status_id') ->references('id')->on('users_status');
            $table->unsignedBigInteger('role_id') ->references('id')->on('users_role');
            $table->unsignedBigInteger('doctor_id') ->references('id')->on('doctors');
            $table->string('name');
            $table->string('last_name')->nullable(); 
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_image');            
            $table->rememberToken();    
            $table->dateTime('last_login')->nullable();       
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('modified_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('status_id')->references('id')->on('users_status');
            $table->foreign('role_id')->references('id')->on('users_role');
            $table->foreign('doctor_id')->references('id')->on('doctors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
