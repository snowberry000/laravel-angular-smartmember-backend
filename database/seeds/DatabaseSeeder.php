<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->doSeed('UserTableSeeder');
         $this->doSeed('InsertUserTable');

        Model::reguard();
    }

    private function doSeed($seed_name)
    {
        $exists = DB::table("table_seeds")->where("seed_name", $seed_name)->count();
        if(!$exists)
        {
            $this->call($seed_name);
            DB::table("table_seeds")->insert(array("seed_name"=>$seed_name));
        }
    }
}
