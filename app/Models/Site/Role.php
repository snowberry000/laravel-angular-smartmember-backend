<?php namespace App\Models\Site;

use App\Models\AccessLevelShareKey;
use App\Models\Root;
use App\Models\User;
use App\Models\AccessLevel\SharedGrant;
use App\Models\Site;
use SMCache;
use Curl;
use App\Models\AccessLevel;
use App\Models\AppConfiguration\SendGridEmail;
use Carbon\Carbon;
use App\Models\AccessLevel\Pass;
use Config;
use App\Models\EmailSubscriber;

class Role extends Root{
    protected $table = 'sites_roles';

    public function site(){
        return $this->belongsTo('App\Models\Site','site_id');
    }

    public function user(){
    	return $this->belongsTo('App\Models\user','user_id');
    }

    public function accessLevel(){
        return $this->belongsTo('App\Models\AccessLevel','access_level_id');
    }

    public static function getRoles($site_id,$user_id){
    	return self::whereSiteId($site_id)->whereUserId($user_id)->get();
    }

    public static function getAccessLevels($site_id,$user_id){
        $unique_access_levels = array();
        $access_levels = self::whereSiteId($site_id)->whereUserId($user_id)->whereNotNull("access_level_id")->where(function ($query) {
            $query->where("expired_at",">", \DB::raw('now()'))
                ->orWhere('expired_at', '=', '0000-00-00 00:00:00')
                ->orWhereNull('expired_at');
        })->get();
        \Log::info($access_levels);
        foreach ($access_levels as $access_level)
        {
            $unique_access_levels = array_merge($unique_access_levels, Pass::access_levels($access_level->access_level_id));
        }

        return array_unique($unique_access_levels);
    }


    public static function getAdminSites($user_id){
    	return self::whereUserId($user_id)->whereIn('type' , ['owner' , 'admin'])->with(['site' , 'site.meta_data'])->get();
    }

    public static function getMemberSites($user_id){
    	return self::whereUserId($user_id)->whereNotIn('type' , ['owner' , 'admin'])->with(['site' , 'site.meta_data'])->get();
    }

    public static function getSites($user_id){
    	$roles = self::with(['site'])->whereUserId($user_id)->get();
        $roles = $roles->toArray();
        $sites = [];

        foreach ($roles as $site){       
                $logo = \App\Models\SiteMetaData::whereSiteId($site['site']['id'])
                        ->whereKey('site_logo')
                        ->first();   

                $site['site']['logo'] = $logo ? $logo->value : "";
                $sites[] = $site;
        }

        return $sites;
    }

    public static function isCustomer($user_id){
		$access_levels = Pass::granted_by_levels(env("MEMBER_ACCESS_LEVEL_ID"));

		$record = Role::whereUserId($user_id)->whereIn('access_level_id', $access_levels )->first();
        if ($record){
            return true;
        }
        
        return false;
    }

	public static function getMembersWithCapability($site_id, $capability){
		$roles = [];
		$system_roles = Config::get('roles.roles');

		foreach( $system_roles as $key=>$val )
			if( in_array( $capability, $val ) )
				$roles[] = $key;

		$custom_roles = Capability::whereCapability($capability)->whereSiteId($site_id)->get();

		foreach( $custom_roles as $custom_role )
			$roles[] = $custom_role->type;

		$users = Role::whereIn('type', $roles)->whereSiteId($site_id)->get()->lists('user_id');

		return $users;
	}

    public static function getFullMembersWithCapability($site_id, $capability){
        $roles = [];
        $system_roles = Config::get('roles.roles');

        foreach( $system_roles as $key=>$val )
            if( in_array( $capability, $val ) )
                $roles[] = $key;

        $custom_roles = Capability::whereCapability($capability)->whereSiteId($site_id)->get();

        foreach( $custom_roles as $custom_role )
            $roles[] = $custom_role->type;

        $users = Role::with(['user','accessLevel'])->whereIn('type', $roles)->whereSiteId($site_id)->get();

        return $users;
    }

    public function applySearchQuery($query , $value){
        $users = User::where('first_name','like','%' . $value . "%")->orWhere('last_name','like','%' . $value . "%")->orWhere('email','like','%' . $value . "%")->select(array('id'))->get();
        $query = $query->whereIn('user_id' , $users);
        return $query;
    }
    
    public static function updatePass($access_pass,$expiration=false){
        if($access_pass){
            $access_level = AccessLevel::find($access_pass->access_level_id);
            if($access_level){

                if( $expiration ) {
                    $access_pass->expired_at = date('Y-m-d H:i:s', $expiration );
                    $access_pass->save();
                    return $access_pass;
                }

                switch ($access_level->payment_interval) {
                    case 'monthly':
                        $access_pass->expired_at = Carbon::now()->addMonths(1);
                        $access_pass->save();
                        return $access_pass;
                        # code...
                        break;
                    case 'weekly':
                        $access_pass->expired_at = Carbon::now()->addWeeks(1);
                        $access_pass->save();
                        return $access_pass;
                        break;
                    case 'bi_weekly':
                        $access_pass->expired_at = Carbon::now()->addWeeks(2);
                        $access_pass->save();
                        return $access_pass;
                        break;
                    case 'annually':
                        $access_pass->expired_at = Carbon::now()->addYears(1);
                        $access_pass->save();
                        return $access_pass;
                        break;

                    default:
                        # code...
                        break;
                }
                return $access_level;
            }
        }
    }

    public static function create(array $data = array())
    {

		$role_data = [
		    'user_id' => $data[ 'user_id' ],
			'site_id' => $data[ 'site_id' ],
			'type' => 'member' ];

		if( !empty( $data['access_level_id'] ) )
			$role_data['access_level_id'] = $data['access_level_id'];

        $pass = self::firstOrNew( $role_data );
        $pass->fill($data);

		if( !empty( $data['access_level_id'] ) )
		{
			$access_level = AccessLevel::find( $data[ 'access_level_id' ] );
			if( $access_level && $access_level->payment_interval == 'one_time' && $access_level->expiration_period )
			{
				$data[ 'expiration_period' ] = Carbon::now()->addMonths( $access_level->expiration_period );
				//dd($data['expiration_period']);
			}

			if( isset( $data[ 'expired_at' ] ) )
			{
				$date = \DateTime::createFromFormat( 'j/m/y', $data[ 'expired_at' ] );
				if( $date )
				{
					$pass->expired_at = $date->format( 'Y-m-d' );
				}
			}
			else if( isset( $data[ 'expiration_period' ] ) )
			{
				//dd($data['expiration_period']);
				$pass->expired_at = $data[ 'expiration_period' ];
			}
		}

        $pass->save();

        return $pass;
    }

    public static function addPersonToWebinar($pass)
    {
        \Log::info('add person to webinar');
        $access_level = AccessLevel::find($pass->access_level_id);
        if (!empty($access_level->webinar_url))
        {
            $user = User::find($pass->user_id);
            //sample webinar link
            //https://attendee.gotowebinar.com/register/5940448390915346946
            $webinar_parts = explode("/", $access_level->webinar_url);
            $webinar_id = array_pop($webinar_parts);
            if (!empty($user->first_name))
            {
                $first_name = $user->first_name;
            } else {
                $first_name = "Annonymous";
            }
            if (!empty($user->last_name))
            {
                $last_name = $user->last_name;
            } else {
                $last_name = "Annonymous";
            }
            if (empty($user->first_name) && empty($user->last_name))
            {
                $existing_subscriber = EmailSubscriber::whereEmail($user->email)->first();
                if ($existing_subscriber && !empty($existing_subscriber->name)) {
                    if (strpos($existing_subscriber->name, " ") !== FALSE)
                    {
                        $name_parts = explode(" ", $existing_subscriber->name);
                        $first_name = $name_parts[0];
                        $last_name = $name_parts[1];
                    }
                    else {
                        $first_name = $existing_subscriber->name;
                        $last_name = "Annoymous";
                    }
                } else {
                    $first_name = "Annonymous";
                    $last_name = "Annonymous";
                }
            }
            \Log::info('Register for webinar' . $webinar_id);
            Curl::post('https://attendee.gotowebinar.com/registration.tmpl', array('registrant.source' => '', 'webinar' => $webinar_id,
                'registrant.givenName' => $first_name, 'registrant.surname' => $last_name, 'registrant.email' => $user->email,
                'registration.submit.button' => 'Register'), array(
                'Content-Type: application/x-www-form-urlencoded'
            ));
        }
    }

    public static function addPersonToAssociateShareAccessLevelKey($pass)
    {
        $shared_access_levels = SharedGrant::whereAccessLevelId($pass->access_level_id)->get();
        $unique_site_ids = [];
        $granted_passes = [];
        $user = User::find($pass->user_id);
        if (count($shared_access_levels) > 0)
        {
            foreach ($shared_access_levels as $shared_access_level)
            {
                $shared_key = AccessLevelShareKey::whereAccessLevelId($shared_access_level->grant_id)->first();
                if (isset($shared_key))
                {
                    $role_data = [
                        'user_id' => $pass->user_id,
                        'site_id' => $shared_key->originate_site_id,
                        'type' => 'member',
                        'access_level_id' => $shared_access_level->grant_id,
                        'expired_at' => $pass->expired_at,
                    ];

                    $pass = self::firstOrNew( $role_data );
                    $pass->fill($role_data);
                    $pass->save();

                    if (!in_array($shared_key->originate_site_id, $unique_site_ids))
                        $unique_site_ids[] = $shared_key->originate_site_id;

                    $pass->site = Site::find($shared_key->originate_site_id);
                    $pass->user = $user;
                    $pass->accessLevel = AccessLevel::find($shared_access_level->grant_id);
                    $pass->password = $user->password;

                    if (!isset($granted_passes[$shared_key->originate_site_id])) {
                        $granted_passes[$shared_key->originate_site_id] = array();
                    }
                    $granted_passes[$shared_key->originate_site_id][] = $pass;

                }
            }


            foreach ($unique_site_ids as $site_id)
            {
                $site = Site::find($site_id);
                SendGridEmail::sendNewUserSiteEmail($user, $site, $user->password, false);
            }

            foreach ($granted_passes as $grant_passes)
            {
                SendGridEmail::sendAccessPassEmail($grant_passes);
            }
        }
    }

	public static function removeSuperLevel( $access_level_id, $user_id )
	{
		$all_the_levels = Pass::access_levels( $access_level_id );

		$sm_2_levels = [ 2684, 2694 ];

		$revoke_all = false;

		foreach( $all_the_levels as $key => $val )
		{
			if( in_array( $val, $sm_2_levels ) )
			{
				$revoke_all = true;
				break;
			}
		}

		if( $revoke_all )
		{
			$subdomains = ['dpp1' , 'dpp2' , 'dpp3' , '3c' , 'help' , 'jv' , 'sm'];
			$chosen_access_level = 'Smart Member 2.0';
			foreach( $subdomains as $key => $subdomain )
			{
				$site = Site::whereSubdomain( $subdomain )->first();
				if( $site && isset( $site->id ) )
				{
					$access_level = AccessLevel::whereSiteId( $site->id )->where( 'name' , '=' , $chosen_access_level )->first();

					if( $access_level && isset( $access_level->id ) )
					{
						$passes = self::whereUserId( $user_id )->whereAccessLevelId( $access_level->id )->get();

						if( $passes )
						{
							foreach( $passes as $pass )
								$pass->delete();
						}
					}
				}
			}
		}

		\App\Models\Event::Log( 'refunded-sm-2-bundle', array(
			'site_id' => 6192,
			'user_id' => $user_id
		) );
	}
}

Role::created(function($pass){
    Role::addPersonToWebinar($pass);
    Role::addPersonToAssociateShareAccessLevelKey($pass);

	if( !empty( $pass->access_level_id ) )
	{
		$all_the_levels = \App\Models\AccessLevel\Pass::access_levels( $pass->access_level_id );

		$sm_2_levels = [ 2684, 2694 ];

		$grant_all = false;

		foreach( $all_the_levels as $key => $val )
		{
			if( in_array( $val, $sm_2_levels ) )
			{
				$grant_all = true;
				break;
			}
		}

		if( $grant_all )
		{
			$data = ['user_id' => $pass->user_id, 'type' => 'member' ];
			$subdomains = ['dpp1' , 'dpp2' , 'dpp3' , '3c' , 'help' , 'jv' , 'sm'];
			$chosen_access_level = 'Smart Member 2.0';
			foreach ($subdomains as $key => $subdomain)
			{
				$site = Site::whereSubdomain($subdomain)->first();
				if($site && isset($site->id)){
					$data['site_id'] = $site->id;
					$access_level = AccessLevel::whereSiteId($site->id)->where('name' , '=' , $chosen_access_level)->first();
					$existing_role = Role::whereUserId( $data['user_id'] )->whereSiteId( $site->id );
					if($access_level && isset($access_level->id))
					{
						$data['access_level_id'] = $access_level->id;
						$existing_role = $existing_role->whereAccessLevelId( $data['access_level_id'] );
					}

					$existing_role = $existing_role->first();

					if( !$existing_role )
						Role::create($data);
				}
			}

			\App\Models\Event::Log( 'received-sm-2-bundle', array(
				'site_id' => 6192,
				'user_id' => $pass->user_id
			) );
		}
	}
});

Role::saved(function($pass){
    Role::addPersonToWebinar($pass);
    Role::addPersonToAssociateShareAccessLevelKey($pass);
    $subdomain = \Domain::getSubdomain();
    $user = User::find($pass->user_id);

    $keys = array();
    $keys[] = $subdomain.':_site_details' . ':'.$user->access_token;
    $keys[] = $subdomain.':_module_home' . ':'.$user->access_token;
    $keys[] = $subdomain.':_user_'.$pass->user_id.':'.$user->access_token;
    \Log::info($keys);
    \SMCache::clear($keys);
});

Role::saving(function($pass){
	$updates = [
		'access_level_id'
	];

	foreach( $pass->getDirty() as $attribute => $value )
	{
		if( in_array( $attribute, $updates ) )
		{
			$original = $pass->getOriginal( $attribute );
			if( $original != $value )
			{
				switch( $attribute )
				{
					case 'access_level_id':
						if( !empty( $original ) && empty( $value ) )
						{
							$pass->access_level_id = $original;
							\App\Models\AppConfiguration\Facebook::removeRefundedMember( $pass );
							Role::removeSuperLevel( $pass->access_level_id, $pass->user_id );
							$pass->access_level_id = $value;
						}
						break;
				}
			}
		}
	}
});

Role::deleted(function($pass){
	//we are going to remove the user from any fb groups that were tied to this access pass
	\App\Models\AppConfiguration\Facebook::removeRefundedMember( $pass );
	Role::removeSuperLevel( $pass->access_level_id, $pass->user_id );
    $subdomain = \Domain::getSubdomain();
    $user = User::find( $pass->user_id );

    $keys = array();
    $keys[] = $subdomain.':_site_details' . ':'.$user->access_token;
    $keys[] = $subdomain.':_module_home' . ':'.$user->access_token;
    $keys[] = $subdomain.':_user_'.$pass->user_id.':'.$user->access_token;

    \SMCache::clear($keys);
});
