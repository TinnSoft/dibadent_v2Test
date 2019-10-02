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

        DB::table('users')->insert([
            'role_id' => 1,
            'status_id' => 1,
            'name' => 'Fernando Ardila',
            'email' => 'fernando2684@gmail.com',
            'password' => '123456.',
        ]);
    }
}
