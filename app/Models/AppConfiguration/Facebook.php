<?php namespace App\Models\AppConfiguration;

use Curl;
use Config;
use App\Models\AppConfiguration;
use Socialite;
use App\Models\User;
use App\Models\Site;
use App\Models\AccessLevel;
use App\Models\AccessLevel\Grant;
use App\Models\AccessLevel\Pass;
use App\Models\UserOptions;


use FacebookRequest;


class Facebook extends AppConfiguration{

    public function __construct(){
    	$this->type = "facebook";
    }

    public static function getAccessToken($code){
        
    }

    public function accessLevels(){
        return $this->hasMany("App\\Models\\Integration\\FBGroupAccessLevel", "facebook_id");
    }

    public function integrate($data){

        return parent::integrate(array(
                "access_token" => $data["access_token"],
                "remote_id" => $data["user_id"],
                "site_id" => $data["site_id"],
            ));
    }

    public static function getAccessLevelOfGroup($site_id)
    {
        $site = \Domain::getSite();
        $user_passes = array();
        $passes = Pass::whereSiteId($site_id)->whereUserId(\Auth::user()->id)->get();

        foreach ($passes as $pass) {
            $user_passes[] = $pass->access_level_id;
        }
        foreach ($passes as $pass) {
            $grants = Grant::whereAccessLevelId($pass->access_level_id)->get();
            foreach ($grants as $grant) {
                $user_passes[] = $grant->grant_id;
            }
        }
        if (!$user_passes && !\SMRole::hasAccess($site->id,'view_restricted_content') )
        {
            return [];
        } else {
            $app_configuration_instance = self::whereSiteId($site_id)->whereType("facebook_group")->first();
            if ($app_configuration_instance)
            {
                $group_access_levels = $app_configuration_instance->accessLevels()->whereIn('access_level_id', $user_passes);
                if ($group_access_levels)
                    return $app_configuration_instance;
            }
        }
    }

    public static function removeGroupMember($site_id,$user_id){
        $app_configuration_instance = self::whereSiteId($site_id)->whereType("facebook_group")->first();
        if ($app_configuration_instance){
            return self::removeGroupMemberByFacebookGroupID($site_id, $app_configuration_instance->remote_id,$user_id);
        }

        return false;
    }

    public static function removeGroupMemberByFacebookGroupID($site_id,$group_id,$user_id){
        $user = User::find($user_id);
        if (!$user->facebook_user_id){
            return false;
        }

        $site = Site::find($site_id);

        $access_token = Curl::get("https://graph.facebook.com/oauth/access_token",array(
                "client_id" => $site->facebook_app_id,
                "client_secret" => $site->facebook_secret_key,
                "grant_type" => "client_credentials"
            ),false);


        $access_token = explode("=", $access_token);
        $access_token = $access_token[1];
        $success = Curl::delete("https://graph.facebook.com/v2.4/{$group_id}/members",array(
                "access_token" => $access_token,
                "member" => $user->facebook_user_id
            ));

        UserOptions::whereUserId($user_id)->whereMetaValue($group_id)->whereMetaKey('fb_group_joined')->delete();

        return $success;
    }

}

?>