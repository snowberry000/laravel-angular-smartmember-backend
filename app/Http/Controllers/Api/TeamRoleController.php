<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\TeamRole;
use App\Models\User;
use App\Models\Site;
use App\Models\Company;
use App\Models\UserRole;
use App\Models\Role;
use Input;
use Auth;


class TeamRoleController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new TeamRole();
         $this->middleware('auth', ['except' => array('migrateTeamRole')]);
        // $this->middleware('admin' , ['except'=>array('index')]);
        // $this->middleware('agent',['except'=>array('getAgents' , 'show','index')]); 
    }

    public function index()
    {
		$page_size = config("vars.default_page_size");
        $current_company_id = $this->getCurrentCompanyId();
        if (!$current_company_id)
              \App::abort(401, 'You must be sign-in to a team to see its members.');
        $page_size = config("vars.default_page_size");
        $query = $this->model->whereCompanyId( $current_company_id )
            ->groupBy( \DB::raw( 'user_id' ) )
            ->take($page_size)
            ->with( 'user' )->has('user');

        foreach (Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
				case 'p':
				case 'view':
				case 'bypass_paging':
					break;
                case 'count':
                    $query->take((Input::get('count')));
                    break;
                case 'order_by':
                    $oby = explode(':', Input::get('order_by'));
                    if( count( $oby ) > 1 && !empty( $oby[0] ) && !empty( $oby[1] ) )
                        $query->orderBy($oby[0], $oby[1]);
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }

		$return = [];

		$return['total_count'] = $query->count();

		if( !\Input::has('bypass_paging') || !\Input::get('bypass_paging') )
			$query = $query->take($page_size);

		if( \Input::has('p') )
			$query = $query->skip((\Input::get('p')-1)*$page_size);

		$roles = $query->get();

        if( $roles )
        {
            foreach( $roles as $role )
            {
                if( !empty( $role->user ) && !empty( $role->user->email ) )
                    $role->email_hash = md5( trim( $role->user->email ) );
            }
        }

		$return['items'] = $roles;

		return $return;
    }

    private function getCurrentCompanyId(){
        $current_company_id = \Auth::user()->options('current_company_id')->first();
        if ($current_company_id){
            $current_company_id = $current_company_id->meta_value;
            return $current_company_id;
        }

        if (!$current_company_id)
        {
            $companies = Company::getUsersCompanies();
            foreach ($companies as $company)
            {
                if ($company->selected == 1)
                {
                    $current_company_id = $company->id;
                    return $current_company_id;
                }
            }
        }
        return null;
    }

    public function addToTeam()
    {
        if( \Auth::user()->isAdmin() )
        {
            if( \Input::get( 'user_id' ) )
            {
                $current_company_id = $this->getCurrentCompanyId();

                $team_role = TeamRole::whereCompanyId( $current_company_id )->whereUserId( \Input::get( 'user_id' ) )
                    ->first();

                if( !$team_role )
                    $team_role = TeamRole::create( [ 'company_id' => $current_company_id, 'user_id' => \Input::get( 'user_id' ), 'role' => 6 ] );

                return $team_role;
            }
        }
    }

    public function updateRole()
    {
        $current_company_id = $this->getCurrentCompanyId();
        $teamRole = $this->model->find(\Input::get('id') );

        $current_user_role = $this->model->whereUserId(\Auth::user()->id)->first();

        if( $current_user_role->role < \Input::get('role') )
        {
            if( $teamRole && $teamRole->company_id == $current_company_id )
            {
                $teamRole->role = \Input::get( 'role' );
                $teamRole->save();

                return $teamRole;
            }
        }
        elseif( $current_user_role->role >= \Input::get('role') )
                \App::abort(400, 'You must have higher access than the role you are trying to assign.');

        return null;
    }

    public function postImport()
    {
        $current_company_id = $this->getCurrentCompanyId();
        if (!$current_company_id)
            App::abort(400, 'You must be sign-in to a team to see its members.');

        $users = [];
        if (\Input::has('emails'))
        {
            $emails = \Input::get('emails');

            $bits = preg_split('/[\ \n\,]+/', $emails );
            
            if( $bits )
            {
                $import_count = 0;

                foreach( $bits as $key => $value )
                {
                    $value = trim( $value );

                    if( !$value )
                        continue;

                    $users[] = $value;
                }
            }   
        }

        $role = 6;
        if (\Input::has('role'))
            $role = \Input::get('role');

        $count = User::importTeamUsers($users, $role , $current_company_id);

        return $count;
    }

    public function migrateTeamRole()
    {

        //migrate company role first
        $roles = Role::with('type')
            ->whereHas('type', function($query){
                $query->where('role_type','<',6);
            })
            ->whereNull('deleted_at')
            ->where('company_id', '!=', 0)
            ->where('site_id','=',0)
            ->get();

        foreach ($roles as $role)
        {
            $current_users_team = TeamRole::whereUserId( $role->user_id )->whereCompanyId( $role->company_id )->first();
            if( !$current_users_team )
                TeamRole::insert(['user_id' => $role->user_id, 'company_id' => $role->company_id, 'role' => $role->type[0]->role_type]);
            else
            {
                if( !empty( $role->type[0]->role_type ) && $role->type[0]->role_type < $current_users_team->role )
                {
                    $current_users_team->role = $role->type[0]->role_type;
                    $current_users_team->save();
                }
            }
        }


        $site_roles = Role::with('type')
            ->whereHas('type', function($query){
                $query->where('role_type','<',6);
            })
            ->where('site_id','!=',0)
            ->get();

        foreach ($site_roles as $site_role)
        {
            $site = Site::find( $site_role->site_id );

            if( $site && !empty( $site->company_id ) )
            {
                $existing_tr = TeamRole::whereUserId( $site_role->user_id )->whereCompanyId( $site->company_id )->first();

                if( !isset( $existing_tr->id ) )
                {
                    TeamRole::insert( [ 'user_id' => $site_role->user_id, 'company_id' => $site->company_id, 'role' => 6 ] );
                }
                else if( isset( $existing_tr->id ) && !empty( $site_role->type[ 0 ]->role_type ) && $existing_tr->role > $site_role->type[ 0 ]->role_type )
                {
                    TeamRole::find( $existing_tr->id )->update( [ 'role' => $site_role->type[ 0 ]->role_type ] );
                }
            }
        }
        //then checkout the rest of the site to migrate missing role
    }

    public function verifyPassword(){
        if (Auth::attempt(['email' => \Auth::user()->email, 'password' => \Input::get("password")]))
        {
            return array('success'=>true , 'message'=>'Password correct');
        }
        return \App::abort(403,"Wrong password");
    }
}
