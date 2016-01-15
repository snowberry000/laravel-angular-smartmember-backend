<?php namespace App\Models\AccessLevel;

use App\Models\Root;
use App\Models\User;
use App\Models\AccessLevel;
use App\Models\Company;
use Carbon\Carbon;
use App\Models\AccessLevel\Grant;

use App\Models\AppConfiguration\Facebook;
use App\Models\EmailSubscriber;

use SMCache;
use Curl;

class Pass extends Root
{
    protected $table = 'access_passes';

    public function accessLevel()
    {
      	return $this->belongsTo("App\\Models\\AccessLevel");
    }

    public function user()
    {
        return $this->belongsTo("App\\Models\\User");
    }

    public function site()
    {
        return $this->belongsTo("App\\Models\\Site");
    }

    public function applySearchQuery($query , $value)
    {
		$levels = AccessLevel::where('name','like','%' . $value . '%')->select('id')->lists('id');

        $users = User::where('email','like','%' . $value . "%")->orwhere('first_name','like','%' . $value . '%')->orwhere('last_name','like','%' . $value . '%')->select(array('id'))->get();

		$query = $query->where(function($q) use ($levels, $users){
			$q->whereIn('user_id' , $users);
			$q->orwhere(function($q2) use ($levels){
				$q2->whereIn('access_level_id', $levels );
			});
		});

		return $query;
    }

	//this returns all the access levels that a given access level really gives access to in an array
	public static function access_levels( $access_level_id, $levels = array() )
	{
		$levels[] = $access_level_id;

		$grants = Grant::whereAccessLevelId( $access_level_id )->get();

		foreach( $grants as $grant )
			if( !in_array( $grant->grant_id, $levels ) )
				$levels = self::access_levels( $grant->grant_id, $levels );

		return array_unique( $levels );
	}

	//this returns all the access levels that a given access level really gives access to in an array
	public static function granted_by_levels( $access_level_id, $levels = array() )
	{
		$levels[] = $access_level_id;

		$grants = Grant::whereGrantId( $access_level_id )->get();

		foreach( $grants as $grant )
			if( !in_array( $grant->access_level_id, $levels ) )
				$levels = self::access_levels( $grant->access_level_id, $levels );

		return array_unique( $levels );
	}

    public function revokePass()
    {
        $group_id = AccessLevel::find($this->access_level_id)->facebook_group_id;
        // Logic pending, currently the older sites have the access pass commented out.
        if($group_id)
            return Facebook::removeGroupMemberByFacebookGroupID($this->site_id,$group_id,$this->user_id);
        return false;
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
        $pass = self::firstOrNew( ['access_level_id' => $data['access_level_id'],
                                  'user_id' => $data['user_id']] );
        $pass->fill($data);

        $access_level = AccessLevel::find($data['access_level_id']);
        if($access_level && $access_level->payment_interval == 'one_time' && $access_level->expiration_period){
            $data['expiration_period'] = Carbon::now()->addMonths($access_level->expiration_period);
            //dd($data['expiration_period']);
        }

        if ( isset($data['expired_at']))
        {
            $date = \DateTime::createFromFormat('j/m/y', $data['expired_at']);
            if ($date){
                $pass->expired_at = $date->format('Y-m-d');
            }
        }
        else if(isset($data['expiration_period'])){
            //dd($data['expiration_period']);
            $pass->expired_at = $data['expiration_period'];
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
}

Pass::created(function($pass){
    \Log::info("Handle pass creation");
    Company::createCompanyUsingPass($pass);
    Pass::addPersonToWebinar($pass);
});


Pass::deleting(function($pass){

    $keys = array();
    $keys[] ='modules' . ':' . $pass->site_id . ':*';
    $keys[] = 'facebook_group_id:'.$pass->site_id . ':*';

    SMCache::clear($keys);

    $routes[] = 'site_details';
    $routes[] = 'module_home';
    $routes[] = 'user_';
    SMCache::reset($routes);
});

Pass::saving(function($pass){
    \Log::info('I am in saving');

    $keys = array();
    $keys[] = 'modules' . ':' . $pass->site_id . ':*';
    $keys[] = 'facebook_group_id:'.$pass->site_id . ':*';

    SMCache::clear($keys);

    $routes[] = 'site_details';
    $routes[] = 'module_home';
    $routes[] = 'user_';

    SMCache::reset($routes);
});

/*Pass::creating(function($pass){

    $pass_key ='modules' . ':' . $pass->site_id . ':*';
    $keys = PRedis::keys($pass_key);
    foreach ($keys as $key)
    {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
    $keys = PRedis::keys('facebook_group_id:'.$pass->site_id . ':*');
    foreach ($keys as $key) {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
});

Pass::updating(function($pass){

    $pass_key ='modules' . ':' . $pass->site_id . ':*';
    $keys = PRedis::keys($pass_key);
    foreach ($keys as $key)
    {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
    $keys = PRedis::keys('facebook_group_id:'.$pass->site_id . ':*');
    foreach ($keys as $key) {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
});*/
