<?php namespace App\Http\Controllers\Api;

use App\Helpers\DomainHelper;
use App\Http\Controllers\ApiController;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\TeamRole;
use App\Models\RoleType;
use App\Models\User;
use App\Models\Site;
use App\Models\Company;
use App\Models\AccessLevel;
use App\Models\AccessLevel\Pass;
use Input;


class RoleController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Role();
        $this->middleware('auth');
        $this->middleware('admin' , ['except'=>array('index','getAgents')]);
        $this->middleware('agent',['except'=>array('getAgents' , 'show','index')]); 
    }

    public function summary() {
        if ( ! $this->site ) return;

        $summary['total_members'] = $this->site->total_members;
        $summary['members_per_day'] = $this->model->getOne(
            "SELECT AVG(members_perday) as members_perday 
                FROM (SELECT count(*) as members_perday FROM roles where site_id = " . 
                $this->site->id . "  group by DATE(created_at)) x", 
            "members_perday");

        $summary["last_member_joined"] = Role::where('site_id', $this->site->id)
                                              ->orderBy('created_at', 'DESC')
                                              ->select(['created_at'])->first();

        $summary["members_today"] = $this->model->getOne(
            "SELECT count(*) as members from roles where site_id = " . $this->site->id . 
            " and DATE(created_at) = CURDATE()", "members");

         $summary["members_yesterday"] = $this->model->getOne(
            "SELECT count(*) as members from roles where site_id = " . $this->site->id . 
            " and DATE(created_at) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))", "members");
         $summary["members_this_week"] = $this->model->getOne(
                "SELECT count(*) as members from roles where site_id = " . $this->site->id .
                " and DATE(created_at) > DATE(DATE_SUB(NOW(), INTERVAL 7 DAY))",
                "members"
            );
         $summary["members_last_week"] = $this->model->getOne(
                "SELECT count(*) as members from roles where site_id = " . $this->site->id .
                " and DATE(created_at) > DATE(DATE_SUB(NOW(), INTERVAL 14 DAY)) ".
                " and DATE(created_at) < DATE(DATE_SUB(NOW(), INTERVAL 7 DAY)) ",
                "members"
            );
         $summary["members_this_month"] = $this->model->getOne(
                "SELECT count(*) as members  from roles where site_id = " . $this->site->id .
                 " and MONTH(created_at) = MONTH(NOW())",
                 "members");
         $summary["members_last_month"] = $this->model->getOne(
                "SELECT count(*) as members  from roles where site_id = " . $this->site->id . 
                " and MONTH(created_at) = MONTH(NOW() - INTERVAL 1 MONTH)",
                "members"
            );

         $summary["members_overtime"] = Role::where('site_id', $this->site->id)
              ->groupBy(\DB::raw("year, month"))
              ->select(\DB::raw("count(*) as members, YEAR(`created_at`) as year, MONTH(`created_at`) as month"))
              ->orderBy(\DB::raw("year, month"))
              ->get();
         
        $summary["recent_members"] = Role::with('type')
                                          ->whereHas('type' , function($query){
                                             $query->where('role_type', '!=',1);
                                          })
                                         ->where('site_id', $this->site->id)
                                         ->with('user')
                                         ->orderBy('created_at', 'DESC')
                                         ->limit(8)
                                         ->get();
        
        return $summary;

    }

    public function outputCSV($data) {

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=members.csv');

        $output = fopen('php://output','w');

        foreach ($data as $datum){
            fputcsv($output, $datum);
        }
        fclose($output);
        exit;

    }

    public function getCSV()
    {

        $site_id = $this->site->id;

        \Input::merge(array('site_id'=>$site_id));

        $query = $this->model->with(["type", "user"]);

        $query = $query->orderBy('id' , 'DESC');
        $query = $query->whereNull('deleted_at');
        foreach (Input::except('access_token') as $key => $value){
            //\Log::info($key);
            switch($key){

                case 'q':
                    //$query = $this->model->applySearchQuery($query,$value);
                    break;
                case 'p':
                    // $page_size = config("vars.default_page_size");
                    // $query = $query->take($page_size);
                    // $query->skip((Input::get('p')-1)*$page_size);
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }

        $roles = $query->get();

        $roles_user_id = $roles->lists('user_id');
        $passes = Pass::whereSiteId($site_id)->whereIn('user_id',$roles_user_id)->get();
        //$users = [];
        foreach ($passes as $pass){
            $users[$pass->user_id] = $pass;
        }

        foreach ($roles as $role) 
        {
            if (isset($users[$role->user_id]))
            {
                $role->access_level = AccessLevel::find($users[$role->user_id]->access_level_id);
                //TODO: I don't know how to make this on global user retrievals
                if (isset($role->user->email))
                {
                    $role->email_hash = md5(trim($role->user->email));
                }
            }

        }

        $arrayCSV = array();

        foreach ($roles as $role) 
        {
            $arrayCSV [] = array($role->user['first_name']." ".$role->user['last_name'],$role->user['email'],$role->access_level['name'],$role['created_at']::parse()->format('d/m/Y'));
        }

        //\Log::info();
        $this->outputCSV($arrayCSV);
        //return $roles;
    }

    public function postImport()
    {
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

        $access_levels = [];
        if (\Input::has('access_levels'))
            $access_levels = \Input::get('access_levels');

        $expiry = \Input::has('expired_at') ? \Input::get('expired_at') : '0';

        $count = User::importUsers($users, array_keys($access_levels), $expiry, $this->site);

        return $count;
    }
    
    public function index()
    {
		$page_size = config("vars.default_page_size");
        $site_id = !empty($this->site) ? $this->site->id : \Input::get('site_id');

        \Input::merge(array('site_id'=>$site_id));

        $query = $this->model->with(["type", "user","type.role_types"]);

        $query = $query->orderBy('id' , 'DESC');
        $query = $query->whereNull('deleted_at');
        foreach (\Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
				case 'access_level':
					$query = $this->model->applyAccessLevelFilter($query,$value);
                case 'p':
                case 'view':
				case 'access_level_status':
                case 'bypass_paging':
                    break;
                case 'count':
                    $query->take((Input::get('count')));
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

        $roles_user_id = $roles->lists('user_id');
        $passes = Pass::whereSiteId($site_id)->whereIn('user_id',$roles_user_id)->get();

        foreach ($passes as $pass){
            $users[$pass->user_id][] = $pass;
        }

        $current_company_id = Company::getOrSetCurrentCompany();

        foreach ($roles as $role)
        {
            if ($role /*&& isset($users[$role->user_id])*/){
                $user = $role->user;
                if ($user){
                     $role->email_hash = 
                        md5(trim($user->email));
                }
            }
            //dd($current_company_id);
            $team_role = TeamRole::whereUserId( $role->user_id )->whereCompanyId( $current_company_id )->first();
            if( $team_role ){
                $role->isTeamMember = true;
            }
            $role->company_name = $current_company_id;
            $role->team_name = $team_role;
            if (isset($users[$role->user_id]))
            {
                $role->access_level = AccessLevel::whereIn( 'id', array_map( function($id){return $id->access_level_id;},$users[$role->user_id] ))->get();

                //TODO: I don't know how to make this on global user retrievals
                if (isset($role->user->email))
                {
                    $role->email_hash = md5(trim($role->user->email));
                }
            }

        }

		$return['items'] = $roles;
  
        return $return;
    }

    public function store()
    {
        $user_id = \Input::get('user_id');
        $role = Role::whereUserId($user_id)->whereSiteId($this->site->id)->first();
        if($role){
            $role->type()->create(array('role_id'=>$role->id , 'role_type'=>\Input::get('role_type')));
			if( \Input::get('role_type') < 6 )
			{
				$team_role = TeamRole::whereUserId( $user_id )->whereCompanyId( $this->site->company_id )->first();
				if( !$team_role )
				{
					TeamRole::create([
						'user_id' => $user_id,
						'company_id' => $this->site->company_id,
						'role' => 6
				 	]);
				}
			}
            return $role;
        }
        $role = parent::store();
        $role->site->total_members = $role->site->total_members + 1;
        $role->site->save();
        return $role;
    }

    public function destroy($model){
        $user_id = $model->user_id;
        Pass::whereSiteId($this->site->id)->whereUserId($model->user_id)->delete();
		$result = parent::destroy($model);

		if( $this->site->total_members > 0 )
		{
			$this->site->total_members = $this->site->total_members - 1;
			$this->site->save();
		}

        return $result;
    }

    public function show($model)
    {
        return $this->model->with(['user' , 'type'])->find($model->id);
    }

    public function getAgents(){
        $current_company_id = Company::getOrSetCurrentCompany();

        $site_ids = Site::whereCompanyId($current_company_id)->select('id')->lists('id')->toArray();

        $agents = Role::with(['type' , 'user'])
            ->whereHas('type' , function($query){
                $query->whereIn('role_type',array(1,2,3,4,5))
                ->distinct();
            })
            ->has('user')
            ->whereIn('site_id',$site_ids)
            ->groupBy('user_id')
            ->get();

        $agent_ids = $agents->map(function($agent){
            return $agent->user_id;
        });

        $team_roles = TeamRole::whereCompanyId( $current_company_id )
                            ->where('role','<',6)
                            ->has('user')
                            ->with('user')
                            ->whereNotIn( 'user_id', $agent_ids )
                            ->get();

        foreach( $team_roles as $team_role )
                TeamRole::addToRolesCollection( $team_role, $agents );

        foreach( $agents as $agent )
        {
            if( empty( $agent->user->profile_image ) )
                $agent->user->profile_image = User::gravatarImage( $agent->user->email );
        }

        return $agents;
    }
}
