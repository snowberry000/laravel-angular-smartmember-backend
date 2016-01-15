<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

class SiteControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $admin_user = $this->newUser(4, 6);
        $agent_user = $this->newUser(5, 6);
        $manager_user = $this->newUser(6, 3);
        $member_user = $this->newUser(6, 0);

        $this->admin_user = $admin_user->id;
        $this->manager_user = $manager_user->id;
        $this->agent_user = $agent_user->id;
        $this->member_user = $member_user->id;

    }

    public function tearDown()
    {

        $users = [$this->admin_user, $this->member_user, $this->manager_user, $this->agent_user];

        App\Models\User::whereIn('id', $users)->forceDelete();
        $roles = App\Models\Role::whereIn('user_id', $users)->get();
        foreach ($roles as $r)
        {
            App\Models\UserRole::where('role_id', $r->id)->forceDelete();
            $r->forceDelete();
        }
        
        App\Models\TeamRole::whereIn('user_id', $users)->forceDelete();
        App\Models\Company::whereIn('user_id', $users)->forceDelete();

        parent::tearDown();
    }

    /**
     * Test site creation
     *
     * @return void
     */
    public function testSitePost()
    {
        // User that should be allowed create sites. 
        $user = App\Models\User::find($this->admin_user);

        $resp = $this->call('POST', '/site?access_token='. $user->access_token, 
                        ['name' => 'Testing', 'subdomain' => str_random(12)]);

        $this->assertResponseOk();

        \DB::table('sites')->where('id', $resp->original['id'])->delete();

    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testSitePostUserNotAllowed()
    {
        // New user
        $user = factory(App\Models\User::class)->create();
        $user->refreshToken();
        $user->save();

        $resp = $this->call('POST', '/site?access_token='. $user->access_token, 
                        ['name' => 'Testing', 'subdomain' => str_random(12)]);

        $user->forceDelete();

    }

    private function newUser($site_role, $team_role)
    {
        $user = factory(App\Models\User::class)->create();
        $user->refreshToken();
        $user->save();


        $r = App\Models\Role::create([
                        'user_id' => $user->id,
                        'site_id' => 1,
                        'company_id' => 1
                    ]);

        App\Models\UserRole::create([
                        'role_id' => $r->id,
                        'role_type' => $site_role,
                        ]);

        App\Models\TeamRole::create([
                        'user_id' => $user->id,
                        'company_id' => 1,
                        'role' => $team_role
                        ]);
        App\Models\Company::create([
                            'name' => 'Test Company',
                            'user_id' => $user->id
                        ]);

        return $user;
    }
    
}
