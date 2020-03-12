<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

       
        DB::table('profiles')->insert([
            'id'=>'1',
            'description' => 'ADMIN'
            ]
        );

        DB::table('profiles')->insert(
            [
            'id'=>'2',
            'description' => 'RADIOLOGO'
            ]
        );

        DB::table('profiles')->insert(
            [
            'id'=>'3',
            'description' => 'DOCTOR'
            ]
        );

        DB::table('genders')->insert([
            'id'=>'1',
            'description' => 'MASCULINO'
            ]
        );

        DB::table('genders')->insert(
            [
            'id'=>'2',
            'description' => 'FEMENINO'
            ]
        );
        DB::table('genders')->insert(
            [
            'id'=>'3',
            'description' => 'NO APLICA'
            ]
        );
        
        DB::table('users')->insert([
            'profile_id' => 1,
            'name' => 'Fernando',
            'email' => 'admin@dibadent.com',
            'password' => bcrypt('123456') // '$2y$10$Q0VM/uttIBoa.C5S5Smvl...xy8XIAe3VDpCvNu/UU8ekKmAk3oHW',
        ]);
 

        /*
        DB::table('profiles')->insert([
            'id'=>'2',
            'description' => 'RADIOLOGO'
        ]);

        DB::table('profiles')->insert([
            'id'=>'3',
            'description' => 'DOCTOR'
        ]);*/
    }
}
