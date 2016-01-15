<?php namespace App\Models\AppConfiguration;

use Curl;
use Config;
use App\Models\AppConfiguration;
use Socialite;

class Paypal extends AppConfiguration{
    	

    public function __construct(){
    	$this->type = "paypal";
    }

    public function getAuthUrl($state){
        return "https://www.sandbox.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize?state=" . $state . "&response_type=code&client_id=" . Config::get('integration.paypal.client_id') . "&scope=openid email&redirect_uri=" . urlencode( ( strpos( env('API_PATH'), 'smartmember.dev' ) === false || strpos( env('API_PATH'), 'smartmember.in' ) ===false? str_replace( 'http://','https://', env('API_PATH') ) : env('API_PATH') ) . '/paypal/access_token' );
    }

    public static function getAccessToken($code){
    	$response = 
	    	Curl::get(
				"https://api.sandbox.paypal.com/v1/identity/openidconnect/tokenservice",
				array(
		    	    'grant_type' => 'authorization_code',
		    	    'client_id' => Config::get('integration.paypal.client_id'),
		    	    'code' => $code,
		    	    'client_secret' => Config::get('integration.paypal.secret_key')
		    	  )
			);

    	if (isset($response["error"])){
    		return false;
    	}
        $userinfo = 
            Curl::post(
                "https://api.sandbox.paypal.com/v1/identity/openidconnect/userinfo/?schema=openid",
                array(),
                array('Authorization:Bearer '.$response['access_token'])
            );
    
        if(isset($userinfo['email']))
            $response['remote_id'] = $userinfo['email'];
    	return $response;
    }

    public function integrate($data){
    	return parent::integrate(array(
    			"access_token" => $data["access_token"],
                "remote_id" => $data['remote_id'],
    			"site_id" => $data["site_id"]
    		));
    }

    public static function processSubscription($data){
        
    }

    public static function processPayment($data){
        
    }
}

?>