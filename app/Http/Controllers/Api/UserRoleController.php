<?php namespace App\Http\Controllers\Api;

use App\Models\AccessLevel\Pass;
use App\Models\User;
use App\Models\TeamRole;
use App\Models\Role;
use App\Models\UserRole;


class UserRoleController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new UserRole();
    }

	public function store()
	{
		if( \Input::has('role_type') && \Input::has('role_id') && \Input::get('role_type') < 6 )
		{
			$role = Role::find( \Input::get('role_id') );

			if( $role )
			{
				$team_role = TeamRole::whereUserId( $role->user_id )->whereCompanyId( $this->site->company_id )
					->first();
				if( !$team_role )
				{
					TeamRole::create( [
						'user_id' => $role->user_id,
						'company_id' => $this->site->company_id,
						'role' => 6
					] );
				}
			}
		}

		return parent::store();
	}

}