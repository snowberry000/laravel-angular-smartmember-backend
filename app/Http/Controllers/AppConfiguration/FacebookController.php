<?php

namespace App\Http\Controllers\AppConfiguration;

use App\Http\Controllers\AppConfigurationController;
use App\Models\AppConfiguration\Facebook;
use App\Models\AppConfiguration;
use App\Models\AppConfiguration\FBGroupAccessLevel;
use App\Models\UserOptions;

use App\Models\Site;
use Input;
use Curl;
use App;

use Socialize;


class FacebookController extends AppConfigurationController
{   
    public function __construct(){
        $this->model = new Facebook();
        parent::__construct();
    }

    public function getTest(){
        return Facebook::removeGroupMember(1,1);
    }

    public function saveGroupAccessLevels($app_configuration_instance_id)
    {
        $app_configuration_instance = Facebook::find($app_configuration_instance_id);
        if (\Input::has('access_levels'))
        {
            $access_levels = \Input::get('access_levels');
            foreach ($access_levels as $key => $value){
                //$link = new FBGroupAccessLevel();
                $app_configuration_instance->accessLevels()->create(['access_level_id' => $value]);
            }
        }
    }

    public function postSetgroup(){

        /*
        $app_configuration_instance = AppConfiguration::whereSiteId($this->site->id)->whereType("facebook_group")->first();
        if ($app_configuration_instance){
            $app_configuration_instance->remote_id = Input::get('group_id');
            $app_configuration_instance->username = Input::get('username');
            $app_configuration_instance->save();

            return $app_configuration_instance;
        }
        */

        $app_configuration_instance = AppConfiguration::create(array(
            "site_id" => $this->site->id,
            "remote_id" => Input::get('group_id'),
            "username" => Input::get('username'),
            "type" => "facebook_group"
        ));
        
        return $app_configuration_instance;
    }

    public function getGroup(){
        $app_configuration_instance = AppConfiguration::whereSiteId($this->site->id)->whereType("facebook_group")->get();
        return $app_configuration_instance ? $app_configuration_instance : [];
    }

    //TODO : Remove socialite code

    public function getAuth($id=''){
        return Socialize::driver('facebook')->with(['state'=>$this->site->subdomain])->scopes(['user_managed_groups'])->redirect();
    }

    public function getCallback(){
        $user = Socialize::with('facebook')->user();
        $data = $this->model->integrate(array('access_token'=>$user->token , 'user_id'=>$user->getId() , 'site_id' =>$this->site->id));

        header('Location: ' . \Domain::appRoute($this->site->subdomain,"/admin/integrations/all")) ;
        exit;
    }

    public function getGroupsJoined()
    {
		$site_id = $this->site->id;

        if ( \SMRole::hasAccess($this->site->id,'view_private_content') )
        {
            $app_configuration_instances = AppConfiguration::where(function($q) use ($site_id){
				$q->where('site_id',$site_id);
			})->whereType('facebook_group')->whereDisabled(0)->get();
            return $app_configuration_instances;
        }

        $groups_joined = UserOptions::whereUserId(\Input::get('user_id'))->whereMetaKey('fb_group_joined')->first();
        if (isset($groups_joined))
        {
            $groups_joined = $groups_joined->meta_value;
            $groups_joined = explode( ',', $groups_joined );

			$app_configuration_instances = AppConfiguration::where(function($q) use ($site_id){
				$q->where('site_id',$site_id);
			})->whereType('facebook_group')->whereDisabled(0)->whereIn('remote_id',$groups_joined)->get();

            return $app_configuration_instances;

        } else {
            return array();
        }
    }

	public function getByGroupId($group_id)
	{
		$site_id = $this->site->id;
		return AppConfiguration::where(function($q) use ($site_id){
			$q->where('site_id',$site_id);
		})->whereType('facebook_group')->whereDisabled(0)->whereRemoteId($group_id)->first();
	}

    public function getGroups(){
		$site_id = $this->site->id;
		$app_configuration_instances = AppConfiguration::where(function($q) use ($site_id){
			$q->where('site_id',$site_id);
		})->whereType('facebook_group')->whereDisabled(0)->get();
        return $app_configuration_instances ? $app_configuration_instances : [];
        
        /*$graph_url= "https://graph.facebook.com/v2.4/me/admined_groups?access_token=".$app_configuration_instance->access_token;
        
        $response = Curl::actual_get($graph_url , array());
        if(isset($response['data']))
            return $response['data'];
        return [];*/
    }

}
