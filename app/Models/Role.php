<?php

namespace App\Models;
use App\Models\AccessLevel\Pass;
use SMCache;

class Role extends Root
{
    protected $table = "roles";
    protected $fillable = ["user_id", "site_id", "company_id", "role_type"];

    public function type()
    {
        return $this->hasMany('App\Models\UserRole');
    }
    
    public function site()
    {
        return $this->belongsTo('App\Models\Site', "site_id");
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public static function incrementVisits($site_id){
        $role = Role::whereSiteId($site_id)->whereUserId(\Auth::user()->id)->first();
        if ($role){
            $role->total_visits = $role->total_visits + 1;
            $role->save();
        }
    }

    public static function incrementLessons($site_id){
        $role = Role::whereSiteId($site_id)->whereUserId(\Auth::user()->id)->first();
        if ($role){
            $role->total_lessons = $role->total_lessons + 1;
            $role->save();
        }
    }

    public function applySearchQuery($query , $value){
        $users = User::where('first_name','like','%' . $value . "%")->orWhere('last_name','like','%' . $value . "%")->orWhere('email','like','%' . $value . "%")->select(array('id'))->get();
        $query = $query->whereIn('user_id' , $users);
        return $query;
    }

	public function applyAccessLevelFilter($query, $value){
		$users = Pass::whereAccessLevelId($value)->select(['user_id'])->lists('user_id');
		if( !\Input::has('access_level_status') || \Input::get('access_level_status') != 1)
			$query = $query->whereNotIn('user_id',$users);
		else
			$query = $query->whereIn('user_id',$users);
		return $query;
	}

    public static function getSites(){
        $companies = Company::getUsersCompanies();
        if(!isset($companies['companies']))
            return null;
        foreach($companies['companies'] as $key=>$company) {
            if (isset($company['selected'])) {
                $selected = $company['id'];
                break;
            }
        }
        if(isset($selected)){
            foreach ($companies['sites'] as $index => $value) {
                if($index==$selected){
                    $selected_site = $value;
                    break;
                }
            }
            if(isset($selected_site)){
                $sites = $companies['sites'][$selected];
                //$sites = Site::whereIn('id',$sites)->get();
                return $sites;
            }
            
        }
        return [];
    }

}

/*Role::creating(function($role){
    
    $role_key ='modules' . ':' . $role->site_id . ':'.$role->user_id;

    $keys = PRedis::keys($role_key);
    foreach ($keys as $key)
    {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
});*/

Role::deleting(function($role){
    $keys = array();
    $keys[] = 'modules' . ':' . $role->site_id . ':'.$role->user_id;
    
    SMCache::clear($keys);

    $routes[] = 'module_home';
    $routes[] = 'user_'.$role->user_id;
    SMCache::reset($routes);
});

/*Role::updating(function($role){
    \Log::info('in role controller');
    $role_key ='modules' . ':' . $role->site_id . ':'.$role->user_id;
    $keys = PRedis::keys($role_key);
    foreach ($keys as $key)
    {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
});*/

Role::saving(function($role){
    \Log::info('I am in saving');

    $keys = array();
    $keys[] ='modules' . ':' . $role->site_id . ':' . $role->user_id;

    SMCache::clear($keys);

    $routes[] = 'module_home';
    $routes[] = 'user_'.$role->user_id;
    SMCache::reset($routes);
});
