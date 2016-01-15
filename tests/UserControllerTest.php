<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * Test site creation
     *
     * @return void
     */
    public function testPostRegister()
    {
        $resp = $this->call('POST', '/auth/register', 
                        [
                            'first_name' => 'Testing', 
                            'last_name' => 'Testing', 
                            'email' => 'test@example.com', 
                            'password' => 'smartmember'
                        ]);

        $resp = $this->call('POST', '/auth/register', 
                        [
                            'first_name' => 'Testing', 
                            'last_name' => 'Testing', 
                            'email' => 'test@example.com', 
                            'password' => 'smartmember'
                        ]);

        $this->assertResponseOk();

        \DB::table('users')->where('id', $resp->original['id'])->delete();

    }

    public function testPostLogin()
    {
        $resp = $this->call('POST', '/auth/register', 
                [
                    'first_name' => 'Testing', 
                    'last_name' => 'Testing', 
                    'email' => 'test@example.com', 
                    'password' => 'smartmember'
                ]);

       $resp = $this->call('POST', '/auth/register', 
                    [
                        'first_name' => 'Testing', 
                        'last_name' => 'Testing', 
                        'email' => 'test@example.com', 
                        'password' => 'smartmember'
                    ]);

        $resp = $this->call('POST', '/auth/login', 
                        [
                            'email' => 'test@example.com', 
                            'password' => 'smartmember'
                        ]);


        $this->assertResponseOk();

        \DB::table('users')->where('id', $resp->original['id'])->delete();
    }
    
}
