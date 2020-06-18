<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRadiologistIdToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
           if(!$table->unsignedBigInteger("radiologist_id")){
              $table->unsignedBigInteger("radiologist_id")->nullable(); 
           };
           //Cómo hacer que radiologist_id sea igual a created_by
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn("radiologist_id");
        });
    }
}
