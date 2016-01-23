<?php namespace App\Models\AppConfiguration;

use Curl;
use Config;
use App\Models\AppConfiguration;
use Socialite;
use App\Models\User;
use App\Models\Site;
use App\Models\Site\Role;
use App\Models\AccessLevel;
use App\Models\AccessLevel\Grant;
use App\Models\AccessLevel\Pass;
use App\Models\UserOptions;
use Carbon\Carbon;

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

	/**
	 * Removes user from FB group(s) for access level and any levels it grants after they are refunded
	 *
	 * Checks if the user is entitled to the group from any other access levels they own first, if not it removes them
	 *
	 * @param object $pass The Site Role object with the access level id
	 */
	public static function removeRefundedMember( $pass )
	{
		if( !empty( $pass->access_level_id ) )
		{
			//get an array of all access levels this one grants including this one
			$access_levels = Pass::access_levels( $pass->access_level_id );

			//just making sure we have access levels
			if( !empty( $access_levels ) )
			{
				//let's loop through each of the access levels the one being revoked granted, and itself too
				foreach( $access_levels as $key => $val )
				{
					//$val is just the access level id, let's get the actual level
					$val = AccessLevel::find( $val );

					//assuming we have the actual access level now let's move forward
					if( $val )
					{
						//we only really care about the level if it has an fb group id set
						if( !empty( $val->facebook_group_id ) )
						{
							$remove_user = true;

							//check to see if there are any other levels that grant access to this facebook group
							$fb_group_levels = AccessLevel::whereFacebookGroupId( $val->facebook_group_id )
								->select( 'id' )
								->get()
								->lists( 'id' );

							//gotta check each level that grants this group
							foreach( $fb_group_levels as $key2 => $val2 )
							{
								//now we need an array of all levels that grant this one
								$levels_that_grant = Pass::granted_by_levels( $val2 );

								//check to see if there are any other roles that grant this
								$role = Role::whereIn( 'access_level_id', $levels_that_grant )
									->where( 'id', '!=', $pass->id )
									->whereUserId( $pass->user_id )
									->where( function ( $query )
									{
										$query->whereNull( 'expired_at' )
											->orWhere( 'expired_at', '>', Carbon::now() )
											->orWhere( 'expired_at', '=', '0000-00-00 00:00:00' );
									} )
									->first();

								//this means we found another access pass for this user that would grant this facebook group, so we don't want to revoke
								if( $role )
									$remove_user = false;
							}

							//we should remove the user, let's do that!
							if( $remove_user )
								self::removeGroupMemberByFacebookGroupID( $pass->site_id, $val->facebook_group_id, $pass->user_id );
						}
					}
				}
			}
		}
	}

	/**
	 * Removes a user from a facebook group, as long as we were able to store their fb user id previously
	 *
	 * @param integer $site_id id of the site we are removing the user from a facebook group for
	 * @param mixed $group_id id of the fb group we are removing the user from
	 * @param integer $user_id id of the user we are removing from the fb group
	 * @return bool
	 */
    public static function removeGroupMemberByFacebookGroupID( $site_id, $group_id, $user_id )
	{
		//first things first, we need the user so we can get their fb user id
        $user = User::find($user_id);

		//if they don't have an fb user id associated with their account there is nothing we can do
		if (!$user->facebook_user_id)
            return false;

		//we need the app configuration for this fb group so that we have the credentials for it
        $app_configuration = AppConfiguration::whereSiteId( $site_id )
			->whereDisabled(0)
			->whereRemoteId( $group_id )
			->first();

		//if the credentials are set on the configuration we can continue
		if( !empty( $app_configuration ) && !empty( $app_configuration->username ) && !empty( $app_configuration->password ) )
		{
			//first we need the access token from fb
			$access_token = Curl::get( "https://graph.facebook.com/oauth/access_token", array(
				"client_id" => $app_configuration->username,
				"client_secret" => $app_configuration->password,
				"grant_type" => "client_credentials"
			), false );

			//make sure we have an access token before we try this next part
			if( !empty( $access_token ) )
			{
				//we need the actual access token
				$access_token = explode( "=", $access_token );

				//as long as we have that access token we can continue
				if( !empty( $access_token[ 1 ] ) )
				{
					$access_token = $access_token[ 1 ];

					//make the curl call to remove the user from the fb group
					$success      = Curl::delete( "https://graph.facebook.com/v2.4/{$group_id}/members", array(
						"access_token" => $access_token,
						"member" => $user->facebook_user_id
					) );

					\App\Models\Event::Log( 'verification-code-sent', array(
						'site_id' => $site_id,
						'user_id' => $user_id,
						'facebook-group-id' => $group_id,
						'user-facebook-id' => $user->facebook_user_id
					) );

					//now let's get the user's fb_group_joined meta so we can remove this group from it
					$user_meta = UserOptions::whereUserId( $user_id )
						->whereMetaKey( 'fb_group_joined' )
						->first();

					//of course make sure we have meta before we try to do stuff with it
					if( !empty( $user_meta ) )
					{
						//we store the fb groups in a comma-separated list, let's turn them into an array
						$groups = explode( ',', $user_meta->meta_value );

						//as long as there are groups to loop through we are going to loop through them
						if( !empty( $groups ) )
						{
							foreach( $groups as $key => $val )
							{
								//if this is the group we just removed them from we want it out of the list
								if( $val == $group_id )
									unset( $groups[ $key ] );
							}

							//set the value back to the comma-separated list of groups without the one we removed them from and save it
							$user_meta->meta_value = implode( ',', $groups );
							$user_meta->save();
						}
					}

					//return whether or not the curl call executed successfully
					return $success;
				}
			}
		}

		//something didn't work, so let's return false
		return false;
    }

}

?>