<?php

namespace App\Models;
use SMCache;
class TeamRole extends Root
{
    protected $table = "team_roles";
    protected $fillable = ["user_id", "company_id", "role"];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function team(){
        return $this->belongsTo('App\Models\Company','company_id');
    }

    public static function addToRolesCollection( $team_role, &$collection )
    {
        $new_role_attributes = array(
            'user_id' => $team_role->user_id,
            'site_id' => 0,
            'company_id' => $team_role->company_id,
            'type' => UserRole::where('id','0')->get()
        );

        if( !empty( $team_role->user ) )
            $new_role_attributes['user'] = $team_role->user;

        $new_role = new Role( $new_role_attributes );

        $new_role->type->add( new UserRole( array(
            'role_type' => $team_role->role,
        ) ));

        $collection->add( $new_role );
    }

    public function applySearchQuery($query , $value){
        $users = User::where('first_name','like','%' . $value . "%")->orWhere('last_name','like','%' . $value . "%")->orWhere('email','like','%' . $value . "%")->select(array('id'))->get();
        $query = $query->whereIn('user_id' , $users);
        return $query;
    }
}

/*TeamRole::creating(function($role){
    
    $company_id = $role->company_id;
    $site_ids = Site::whereCompanyId($company_id)->get(['id']);
    
    foreach ($site_ids as $key => $value) {
        \Log::info($value);
        $role_key ='modules' . ':' . $value['id'] . ':'.$role->user_id;
        $keys = PRedis::keys($role_key);
        foreach ($keys as $key)
        {
            \Log::info("Deleting " . $key);
            PRedis::del($key);
        }
    }
});*/

TeamRole::deleting(function($role){
    $company_id = $role->company_id;
    $site_ids = Site::whereCompanyId($company_id)->get(['id']);
    $keys = array();
    foreach ($site_ids as $key => $value) {
        \Log::info($value);
        $role_key ='modules' . ':' . $value['id'] . ':'.$role->user_id;
        $keys[] = $role_key;
    }
    SMCache::clear($keys);

    $routes[] = 'user_'.$role->user_id;
    $routes[] = 'site_details';
    $routes[] = 'module_home';
    SMCache::reset($routes);
});

TeamRole::saving(function($role){
    
    $company_id = $role->company_id;
    $site_ids = Site::whereCompanyId($company_id)->get(['id']);
    $keys = array();
    foreach ($site_ids as $key => $value) {
        $role_key ='modules' . ':' . $value['id'] . ':'.$role->user_id;
        $keys[] = $role_key;
    }
    SMCache::clear($keys);

    $routes[] = 'site_details';
    $routes[] = 'module_home';
    $routes[] = 'user_'.$role->user_id;
    SMCache::reset($routes);
});
