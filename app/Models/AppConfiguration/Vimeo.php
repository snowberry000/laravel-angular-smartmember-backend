<?php namespace App\Models\AppConfiguration;

use App\Models\AppConfiguration;
use App\Models\ConnectedAccount;
use App\Models\Site;
use Config;

class Vimeo extends AppConfiguration {
    
    protected $library;

    public function __construct(){
    	$this->type = "vimeo";
    	$this->library = new \Vimeo\Vimeo(\Config::get("integration.vimeo.client_id"), \Config::get("integration.vimeo.client_secret"));
    }

    public function getAuthURL($state){
    	$redirect_url = \Domain::apiPath('/vimeo/auth');
        return $this->library->buildAuthorizationEndpoint($redirect_url, "public",$state);
    }

    public function getAccessToken($code){
    	 return $this->library->accessToken($code, \Domain::apiPath('/vimeo/auth'));
    }

    public function integrate($data , $subdomain = '' , $user_id = 0){
		$site = Site::whereSubdomain($subdomain)->first();
		$new_data = array();
		$new_data['type'] = 'vimeo';
		$new_data['site_id'] = $site->id;
		$new_data['access_token'] = $data["body"]["access_token"];
		$new_data['remote_id'] = $data["body"]["user"]["uri"];
        $new_data['account_id'] = $user_id; 
		$account = ConnectedAccount::whereType('vimeo')->whereSiteId($site->id)->whereRemoteId( $new_data['remote_id'] )->first();
		if( $account ){
			$account->access_token = $new_data['access_token'];
            $account->account_id = $new_data['account_id'];
        }
		else
			$account = ConnectedAccount::firstOrCreate($new_data);

		$account->save();

		return $account;
    }

    public static function mapToLesson($data){

		$url = $data['uri'];
		$video_id = explode( '/', $url );
		$video_id = array_pop( $video_id );

        $lesson = array(
            "title" => isset($data["name"]) ? $data["name"] : '',
            "featured_image" => isset($data["pictures"]["sizes"][3]["link"]) ? $data["pictures"]["sizes"][3]["link"] : '',
            "embed_content" => !empty($data["embed"]["html"]) ? $data["embed"]["html"] : '<iframe src="https://player.vimeo.com/video/' . $video_id . '?badge=0&autopause=0&player_id=0" width="1280" height="720" frameborder="0" title="Affiliate Marketing Blueprint" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
            "remote_id" => $data["uri"],
            "type" => "vimeo"
        );
        $lesson["content"] = isset($data["description"]) ? $data["description"] : $data["name"];

        return $lesson;
    }

}

?>