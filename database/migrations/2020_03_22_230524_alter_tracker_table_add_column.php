<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTrackerTableAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('tracker', 'read')) {
            Schema::table('tracker', function (Blueprint $table) {
                $table->boolean('read')->after('model')->default(false);        
             });
        }

        if (!Schema::hasColumn('tracker', 'value')) {
            Schema::table('tracker', function (Blueprint $table) {
                $table->bigInteger('value')->after('model')->nullable();         
             });
        }

        if (!Schema::hasColumn('tracker', 'deleted_at')) {
            Schema::table('tracker', function (Blueprint $table) {
                $table->dateTime('deleted_at')->after('updated_at')->nullable();   
             });
        }

        if (!Schema::hasColumn('tracker', 'notify')) {
            Schema::table('tracker', function (Blueprint $table) {
                $table->boolean('notify')->after('read')->default(false);        
             });
        }
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracker', function (Blueprint $table) {
           $table->dropColumn('read'); 
           $table->dropColumn('value'); 
           $table->dropColumn('deleted_at'); 
           $table->dropColumn('notify'); 
        });
    }
}
