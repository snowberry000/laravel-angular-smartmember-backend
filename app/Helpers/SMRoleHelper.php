<?php namespace App\Helpers;

use App\Models\Site\Role;
use App\Models\Site\CustomRole;

use Auth;
use Config;

class SMRoleHelper{

	public static function hasAccess($site_id, $capability){
		$user = self::__getCurrentUser();			
		if (!$user){
			return false;
		}
		$capabilities = self::getUserCapabilities($site_id,$user->id);

		if (in_array($capability, $capabilities)){
			return true;
		}
		return false;
	}

	public static function userHasAccess($site_id, $capability, $user_id){
		$capabilities = self::getUserCapabilities($site_id,$user_id);

		if (in_array($capability, $capabilities)){
			return true;
		}
		return false;
	}

	private static function __getCurrentUser(){
		if (!Auth::user() && !SMAuthenticate::set()){
			return false;
		}
		return Auth::user();
	}

	public static function isCustomer($user_id){
		return Role::isCustomer($user_id);
	}

	public static function getUserCapabilities($site_id,$user_id){
		$capabilities = [];
		$user_roles = Role::getRoles($site_id,$user_id);
		$system_roles = Config::get('roles.roles');

		foreach ($user_roles as $role){
			if (isset($system_roles[$role->type])){
				$capabilities = array_merge($capabilities,$system_roles[$role->type]);
			}else{
				$capabilities = array_merge($capabilities, CustomRole::getCapabilities($site_id, $role->type));
			}
		}
		return $capabilities;
	}

	public static function getUserAccessLevels($site_id,$user_id){
		$access_levels = Role::getAccessLevels($site_id,$user_id);
		$levels = [];

		foreach ($access_levels as $key => $value){
			$levels[] = $value;
		}
		return $levels;
	}

}