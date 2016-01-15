<?php namespace App\Models;

use App\Models\Role;
use App\Models\TeamRole;
use App\Helpers\SMAuthenticate;
use App\Models\UserOptions;
use App\Models\User;
use App\Models\Site;
use App\Models\Download;
use App\Models\AccessLevel\Pass;
use App\Models\AccessLevel\Grant;
use PRedis;
use App\Http\Controllers\Api\SiteController;
use SMCache;
class Company extends Root
{
    protected $table = 'companies';
    
    public function sites(){
    	return $this->hasMany('App\\Models\\Site','company_id');
    }

    public function members(){
        return $this->hasMany('App\\Models\\TeamRole','company_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function options($meta_key = null)
    {
        if (!empty($meta_key))
            return $this->hasMany("App\\Models\\CompanyOptions", "company_id")->where('meta_key', $meta_key);

        return $this->hasMany("App\\Models\\CompanyOptions", "company_id");
    }

    /**
     * This will either retrieve the current company id or it will set it if it hasn't been set.
     *
     * @param int $backup_company_id this is the fall back company id to use in the event there is no current company id and there is no current site (i.e. you are on my.smartmember)
     * @return int|null integer if there is a current company id or one was able to be set, null otherwise.
     */
    public static function getOrSetCurrentCompany($backup_company_id=0)
    {
        $current_company_id = null;

        $user = \Auth::user();
        if(!isset($user)){
            SMAuthenticate::set();
            $user = \Auth::user();
        }
        if(!isset($user))
            return null;

        $current_company = $user->options('current_company_id')->first();
        if ($current_company)
            $current_company_id = $current_company->meta_value;
        else
        {
            $subdomain = \App\Helpers\DomainHelper::getSubdomain();

            if( !empty( $subdomain ) )
                $site = \App\Models\Site::whereSubdomain( $subdomain )->first();

            if( !empty( $site ) )
                $company_id = $site->company_id;
            elseif( !empty( $backup_company_id ) )
                $company_id = $backup_company_id;

            if( !empty( $company_id ) )
            {
                UserOptions::create( [ 'user_id' => \Auth::user()->id,
                                       'meta_key' => 'current_company_id',
                                       'meta_value' => $company_id
                                     ] );

                $current_company_id = $company_id;
            }
        }

        return $current_company_id;
    }

	public static function getUserCompaniesAndSites()
	{
		//first authenticate the user and cancel out if  there isn't one
		$user = \Auth::user();
		if(!isset($user)){
			SMAuthenticate::set();
			$user = \Auth::user();
		}
		if(!isset($user))
			return null;

		//get all the team roles for the current user
		$team_roles = TeamRole::whereUserId( $user->id )->get();

		//set up empty arrays for companies and the company sites
		$companies = $company_sites = [];

		foreach( $team_roles as $key=>$val )
		{
			$company = Company::find( $val->company_id );

			if ($company && isset($company->id))
			{
				$companies[] = $company;

				$sites = \App\Models\Site::whereCompanyId( $company->id );

				if( $val->role > 5 )
				{
					$site_ids = Site::whereCompanyId( $company->id )->select('id')->lists('id');

					$site_roles = Role::whereUserId( $user->id )
						->whereIn('site_id',$site_ids)
						->with('type')
						->whereHas('type' , function($q){
							$q->where('role_type', '<', 6);
						})->select('site_id')->lists('site_id');

					$sites = $sites->whereIn('id',$site_roles);
				}

				$sites = $sites->get();

				if( $sites )
				{
					foreach( $sites as $site )
					{
						$company_sites[] = $site;
					}
				}
			}
		}

		return array("companies" => $companies, "sites" => $company_sites);
	}

    public static function getTeamMemberOfCompany($company_id)
    {
        $team_roles = TeamRole::with(['user' => function($query) {
            $query->select('id','first_name','last_name','email','profile_image','email','email_hash');
        }])->whereCompanyId( $company_id )->get();
        return $team_roles;
    }

    public static function getUsersCompanies()
    {
        //first authenticate the user and cancel out if  there isn't one
        $user = \Auth::user();
        if(!isset($user)){
            SMAuthenticate::set();
            $user = \Auth::user();
        }
        if(!isset($user))
            return null;

        //get all the team roles for the current user
        $team_roles = TeamRole::whereUserId( $user->id )->whereNull('deleted_at')->orderBy('role', 'asc')->get();

        //set up empty arrays for companies and the company sites
        $companies = $company_sites = [];

        foreach( $team_roles as $key=>$val )
        {
            $company = Company::find( $val->company_id );

            if ($company && isset($company->id))
            {
                if (!array_key_exists($company->id, $company_sites))
                {
                    $company_sites[$company->id] = [];
                    $companies[] = $company;
                }

                $sites = \App\Models\Site::whereCompanyId( $company->id );

                if( $val->role > 5 )
                {
                    $site_ids = Site::whereCompanyId( $company->id )->select('id')->lists('id');

                    $site_roles = Role::whereUserId( $user->id )
                                    ->whereIn('site_id',$site_ids)
                                    ->whereNull('deleted_at')
                                    ->with('type')
                                    ->whereHas('type' , function($q){
                                        $q->where('role_type', '<', 6);
                                    })->select('site_id')->lists('site_id');

                    $sites = $sites->whereIn('id',$site_roles);
                }

                $sites = $sites->get();

                if( $sites )
                {
                    foreach( $sites as $site )
                    {
                        if (!in_array($site->id, $company_sites[$company->id]))
                        {
                            $company_sites[$company->id][] = $site->id;
                        }
                    }
                }
            }
        }

        if (count($companies) > 0)
        {
            $current_company_id = self::getOrSetCurrentCompany($companies[0]->id);
            $current_company_set_flag = false;
            foreach( $companies as $company )
            {
                if( $current_company_id == $company->id )
                {
                    $current_company_set_flag = true;
                    $company->selected = 1;
                    $download = new Download();
                    $total_lessons = $total_downloads = $total_members = $total_sites = $total_revenue = 0;
                    foreach ($company_sites[$current_company_id] as $site_info)
                    {
                        $site = Site::find($site_info);
                        $total_members += $site->total_members;
                        $total_lessons += $site->total_lessons;
                        $total_downloads += $download->getOne("select count(id) as total_downloads FROM download_center WHERE site_id = " . $site_info . " and access_level_type = 4 and deleted_at = NULL", 'total_downloads');
                        $total_revenue += $site->total_revenue;
                        $total_sites++;
                    }
                    $company->team_members = Company::getTeamMemberOfCompany($company->id);
                    $company->total_members = $total_members;
                    $company->total_lessons = $total_lessons;
                    $company->total_downloads = $total_downloads;
                    $company->total_revenue = $total_revenue;
                    $company->total_sites_count = $total_sites;
                    $company->hide_total_lessons = intval($company->hide_total_lessons);
                    $company->hide_members = intval($company->hide_members);
                    $company->hide_total_downloads = intval($company->hide_total_downloads);
                    $company->hide_revenue = intval($company->hide_revenue);
                    $company->hide_sites = intval($company->hide_sites);

                } else {
                    $company_team_role = $team_roles->where('company_id', $company->id)->first();
                    if ($company_team_role->role <= 3)
                    {
                        $download = new Download();
                        $total_lessons = $total_downloads = $total_members = $total_sites = $total_revenue = 0;
                        foreach ($company_sites[$company->id] as $site_info)
                        {
                            $site = Site::find($site_info);
                            $total_members += $site->total_members;
                            $total_lessons += $site->total_lessons;
                            $total_downloads += $download->getOne("select count(id) as total_downloads FROM download_center WHERE site_id = " . $site_info . " and access_level_type = 4 and deleted_at = NULL", 'total_downloads');
                            $total_revenue += $site->total_revenue;
                            $total_sites++;
                        }
                        $company->team_members = Company::getTeamMemberOfCompany($company->id);
                        $company->total_members = $total_members;
                        $company->total_lessons = $total_lessons;
                        $company->total_downloads = $total_downloads;
                        $company->total_revenue = $total_revenue;
                        $company->total_sites_count = $total_sites;
                        $company->hide_total_lessons = intval($company->hide_total_lessons);
                        $company->hide_members = intval($company->hide_members);
                        $company->hide_total_downloads = intval($company->hide_total_downloads);
                        $company->hide_revenue = intval($company->hide_revenue);
                        $company->hide_sites = intval($company->hide_sites);
                    }
                }
                $company->initials = $company->name;
                /*if (!empty($company->name))
                {
                    if (strpos(trim($company->name), ' ') !== FALSE)
                    {
                        $name_bits = explode( ' ', $company->name );
                        $company->initials = count( $name_bits ) > 1 ? $name_bits[0][0] . $name_bits[1][0] : $name_bits[0][0] . ( !empty( $name_bits[0][1] ) ? $name_bits[0][1] : '' );
                    } else {
                        $company->initials = substr($company->name, 0, 2);
                    }
                } else {
                    $name_bits = explode( '@' , $user->email);
                    $company->initials = $name_bits[0][0];
                }*/

            }

            if (!$current_company_set_flag)
            {
                $companies[0]->selected = 1;
                $user_option = UserOptions::where('user_id', $user->id)->whereMetaKey('current_company_id')->first();
                if ($user_option)
                {
                    $user_option->meta_value == $companies[0]->id;
                    $user_option->save();
                }
            }
        }


        //this can be used to sort the companies, but it increases the response time by a couple seconds
        /*usort( $companies, function($a,$b){
            if( $a->name > $b->name )
                return 1;
        });*/
        return array("companies" => $companies, "sites" => $company_sites);
    }

	public static function getUsersSitesAndTeams()
	{
		//first authenticate the user and cancel out if  there isn't one
		$user = \Auth::user();
		if(!isset($user)){
			SMAuthenticate::set();
			$user = \Auth::user();
		}
		if(!isset($user))
			return null;

		//get all the team roles for the current user
		$team_roles = TeamRole::whereUserId( $user->id )->orderBy('role', 'asc')->get();

		//set up empty arrays for companies and the company sites
		$companies = $company_sites = [];

		$admin_site_ids = [];

		foreach( $team_roles as $key=>$val )
		{
			$company = Company::find( $val->company_id );

			if ($company && isset($company->id))
			{
				if (!array_key_exists($company->id, $company_sites))
				{
					$company_sites[$company->id] = [];

					$sites = \App\Models\Site::whereCompanyId( $company->id );

					if( $val->role > 5 )
					{
						$site_ids = Site::whereCompanyId( $company->id )->select('id')->lists('id');

						$site_roles = Role::whereUserId( $user->id )
							->whereIn('site_id',$site_ids)
							->with('type')
							->whereHas('type' , function($q){
								$q->where('role_type', '<', 6);
							})->select('site_id')->lists('site_id');

						$sites = $sites->whereIn('id',$site_roles);

						foreach( $site_roles as $role )
							$admin_site_ids[] = $role;
					}

					$company->sites = $sites->with('meta_data')->get();

					foreach( $company->sites as $site )
						$admin_site_ids[] = $site->id;

					$companies[] = $company;
				}
			}
		}

		$member_roles = Role::with('type')
			->whereHas('type', function($query){
				$query->where('role_type', 6);
			})
			->whereUserId(\Auth::user()->id)
			->where('site_id', '!=', 0)
			->where('site_id', '!=', 6192);

		if( !empty( $admin_site_ids ) )
			$member_roles = $member_roles->whereNotIn('site_id', $admin_site_ids );

		$ids = $member_roles->lists('site_id');

		$members = Site::with('meta_data')
			->whereIn('id', $ids)
			->orderBy('total_revenue', 'DESC');
		$members = $members->get();


		foreach ($members as $member)
		{
			$member['access'] = Pass::with('accessLevel')->where('user_id', \Auth::user()->id)->where('site_id', $member->id)->get();
		}

		return array('admin' => $companies, 'member' => $members);
	}

    public static function getNewCurrentCompany($site)
    {
        if( isset( $site ) )
        {
            $company = Company::getCurrentSiteCompany( $site );
        }
        else
        {
            $user = \Auth::user();
            $role = Role::whereUserId($user->id)->whereSiteId(0)->where('company_id','!=',0)->first();

            if( $role )
                $company = Company::find($role->company_id);
            else
                $company = Company::whereUserId( $user->id )->first();
        }
        return $company;
    }

    public static function getCurrentCompany($site, $user)
    {
    	$company_id = 0;

    	if (!$site or !$user)
    		return;

    	$ids = Role::with('type')
                   ->whereHas('type' , function($query){
                      $query->where('role_type', 1);
                   })
                    ->where('site_id', $site->id)
                    ->select(array('user_id'))
                    ->first();

    	$id = isset($ids) ? $ids->user_id : $user->id;

    	$company_id = Role::with('type')
                           ->whereHas('type' , function($query){
                              $query->where('role_type', 1);
                           })
                            ->where('user_id', $id)
                            ->where('company_id', '!=', 0)
                            ->select(['company_id'])
                            ->first();

        $company = null;
        if( !empty( $company_id ) )
            $company = Company::find( $company_id->company_id );

        if( empty($company) )
            $company = Company::whereUserId($id)->select(['id','hash'])->first();

    	return $company;
    }

    public static function getCurrentSiteCompany($site)
    {
        $company_id = 0;

        if( is_int( $site ) || is_string( $site ) )
        {
            $site = \App\Models\Site::find( $site );
        }

        if (!$site)
            return;

        $site_id = $site->id;

        if( !empty( $site->company_id ) )
        {
            $company_id = $site->company_id;
        }
        else
        {
            $id = Role::with( 'type' )
                ->whereHas( 'type', function ( $query )
                {
                    $query->whereIN( 'role_type', [ 1, 2 ] );
                } )
                ->where( 'site_id', $site_id )
                ->select( array( 'user_id' ) )
                ->first();

            if( !isset($id ))
                return;

            $id = $id->user_id;

            $company_id = Role::with( 'type' )
                ->whereHas( 'type', function ( $query )
                {
                    $query->whereIN( 'role_type', [ 1, 2 ] );
                } )
                ->where( 'user_id', $id )
                ->where( 'company_id', '!=', 0 )
                ->select( [ 'company_id' ] )
                ->first();

            $company_id = $company_id->company_id;
        }

        $company = null;
        if( isset( $company_id ) )
            $company = Company::find( $company_id );

        if( empty($company) )
		{
			if( !$id ){
				$error = array("message" => 'This site doesnt appear to be associated with a team, please contact support.', "code" => 500);
				return response()->json($error)->setStatusCode(500);
			}

			$company = Company::whereUserId( $id )->first();
		}

        if ( isset($company) && empty($company->name) )
        {
            $user = User::find($id);
            $company->name = $user->first_name . (!empty($user->last_name) ? ' '. $user->lastname : '') . '\'s Team';
            $initials = explode( ' ', $company->name );
            $company->initials = substr( $initials[ 0 ], 0, count( $initials ) > 2 ? 1 : 2 ) .
                                 ( count( $initials ) > 2 ? substr( $initials[ 1 ], 0, 1 ) : '' );
        }
        
        return $company;
    }

    public static function setPermalink($model)
    {
        $text = 'team';

        if (!$model->permalink)
        {
            $model->display_name = $model->display_name ? $model->display_name : $model->name;
            $text = str_replace(" ", "-", trim($model->display_name));
        }else{
            \Log::info($model);
            $text = str_replace(" ", "-", trim($model->permalink));
        }
        
        $text = self::handleDuplicatePermalink($model, $text);

        \Log::info("Permalink " . $text);

        return $text;
    }

    public static function handleDuplicatePermalink($model, $text)
    {
        $if_exists = Company::wherePermalink($text)->first();
        if ($if_exists && (!$model->permalink)){
            $last_char = $text{strlen($text)-1};
            if ($text{strlen($text)-2} == '-' && is_numeric($last_char))
            {
                 $last_char = intval($last_char) + 1;
                 $text{strlen($text)-1} = $last_char;
            }
            else
            {
                $text .= "-1";
            }

            return self::setPermalink($model, $text);
        }
        return $text;
    }

    public static function createCompanyUsingPass($pass)
    {
        $required_level = \Config::get('vars.member_access_level', 1753);

        \Log::info($required_level);

		//$access_levels = Pass::access_levels( $pass->access_level_id );

        //if ( in_array($required_level, $access_levels ) ) /*This is taking too much time to process */
        if ($required_level == $pass->access_level_id) //since sm pass is not granted we check for it directly
        {
            \Log::info('we create company');
            $role = TeamRole::whereUserId( $pass->user_id )->where('role','<',2)->has('team')->first();
            if (! $role)
            {
                \Log::info('should not have any role');
                $user = User::find($pass->user_id);
                $hash = md5 ( microtime() . rand(0,1000) );

				if( !empty( $user->first_name ) )
                	$teamName = $user->first_name . (!empty($user->last_name) ? ' '. $user->last_name : '') . '\'s Team';
				else
					$teamName = $user->email . '\'s Team';

                $company_data = array (
                    'name' => $teamName,
                    'user_id' => $user->id,
                    'hash' => $hash
                );

                $company = Company::whereUserId($user->id)->whereNull('deleted_at')->first();

                \Log::info('company is created');

                if (!$company)
                    $company = Company::create( $company_data );

                \Log::info('company is done created');
                $role = TeamRole::create([
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                        'role' => 1
                    ]);

                \Log::info('role is created');
				$current_company = $user->options('current_company_id')->first();
				if ($current_company)
				{
					$current_company->meta_value = $company->id;

					$current_company->save();
				}
				else
				{
					UserOptions::insert( [ 'user_id' => $user->id,
										   'meta_key' => 'current_company_id',
										   'meta_value' => $company->id
										 ] );
				}
                \Log::info('user options is created');
            }
        }
        \Log::info('we done create company');
    }
}

Company::updating(function($company){
    \Log::info($company);

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    $routes[] = 'user_';
    SMCache::reset($routes);
    return $company;
});

Company::saving(function($company){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    $routes[] = 'user_';
    
    SMCache::reset($routes);
    return $company;
});

Company::saved(function($company){
	$site_controller = new SiteController();

	$site_controller->setCachedCompanyDetails( $company->id );
});

/*Company::created(function($company){
    $company->permalink = Company::setPermalink($company);
    $company->save();
    return $company;
});*/

