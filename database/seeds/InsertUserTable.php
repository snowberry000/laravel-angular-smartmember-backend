<?php

use Illuminate\Database\Seeder;

class InsertUserTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'test@smartmember.com',
            'verified' => true,
            'password' => Hash::make("john"),
            'access_token' => '9f92fb02ecc361000c2ae3ce0982424f',
            'access_token_expired' => '2030-12-12 00:00:00'
        ]);
    }
}
