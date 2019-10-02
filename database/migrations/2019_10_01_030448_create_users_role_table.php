<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_role', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('role');
        });

        
         DB::table('users_role')->insert(
            array(
            'role' => 'ADMIN'
            )
        );
         DB::table('users_role')->insert(
            array(
            'role' => 'RADIOLOGO'
            )
        );
         DB::table('users_role')->insert(
            array(
            'role' => 'DOCTOR'
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
        Schema::dropIfExists('users_role');
    }
}
