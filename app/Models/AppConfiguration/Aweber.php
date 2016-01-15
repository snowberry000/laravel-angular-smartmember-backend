<?php namespace App\Models\AppConfiguration;

use App\Models\AppConfiguration;
use App\Models\ConnectedAccount;
use Config;
use App\Helpers\OAuthApplication;

class AweberAppConfiguration extends AppConfiguration {
    
    protected $library;

    public function __construct(){
    	$this->type = "aweber";
    	$this->library = new OAuthApplication(\Config::get("integration.aweber.consumer_key"), \Config::get("integration.aweber.consumer_secret"));
    }

    public function getAuthURL(){
        return $this->library->getAuthorizeUrl();
    }

    public function getAccessToken(){
    	 return $this->library->getRequestToken(\Domain::apiPath('/aweber/auth'));
    }

    public function integrate($data){
		$site = \Domain::getSite();
		$this->library->user->requestToken = $data['oauth_token'];
		$this->library->user->verifier = $data['oauth_verifier'];
		$this->library->user->tokenSecret = $_SESSION['aweber_secret'];
		list($accessKey, $accessSecret) = $this->library->getAccessToken();
		$new_data = array();
		$new_data['type'] = 'aweber';
		$new_data['site_id'] = $site->id;
		$new_data['remote_id'] = $accessKey;
		$new_data['access_secret'] = $accessSecret;

		$account = ConnectedAccount::whereType('aweber')->whereSiteId( $site->id )->whereRemoteId( $new_data['remote_id'] )->first();
		if( $account )
			$account->access_token = $new_data['access_secret'];
		else
			$account = ConnectedAccount::firstOrCreate($new_data);
		$account->save();

		return $account;
    }
}

?>