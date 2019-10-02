<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description');
            
        });

         DB::table('users_status')->insert(
            array(
            'description' => 'ACTIVE'
            )
        );
         DB::table('users_status')->insert(
            array(
            'description' => 'INACTIVE'
             )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_status');
    }
}
