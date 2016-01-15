<?php

namespace App\Models;
use SMCache;
class UserRole extends Root
{
    protected $table = "user_roles";

    public function role_types()
    {
        return $this->belongsTo('App\Models\RoleType' , 'role_type');
    }
}

/*UserRole::creating(function($role){
    $role = Role::find($role->role_id);
    $role_key ='modules' . ':' . $role->site_id . ':'.$role->user_id;

    $keys = PRedis::keys($role_key);
    foreach ($keys as $key)
    {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
});*/

UserRole::deleting(function($role){
	$role = Role::find($role->role_id);
    $keys = array();
    $keys[] ='modules' . ':' . $role->site_id . ':'.$role->user_id;
    
    SMCache::clear($keys);
    $routes[] = 'module_home';
    $routes[] = 'user_'.$role->user_id;
    SMCache::reset($routes);
});

/*UserRole::updating(function($role){
	$role = Role::find($role->role_id);
    
    $role_key ='modules' . ':' . $role->site_id . ':'.$role->user_id;
    $keys = PRedis::keys($role_key);
    foreach ($keys as $key)
    {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
});*/

UserRole::saving(function($role){
    \Log::info('I am in saving');
    $role = Role::find($role->role_id);
    $keys = array();
    $keys[] ='modules' . ':' . $role->site_id . ':' . $role->user_id;

    SMCache::clear($keys);
    $routes[] = 'module_home';
    $routes[] = 'user_'.$role->user_id;
    SMCache::reset($routes);
});
