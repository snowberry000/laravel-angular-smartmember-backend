<?php namespace App\Models\AppConfiguration;

use Curl;
use Config;
use App\Models\AppConfiguration;
use App\Models\Site;
use App\Models\ConnectedAccount;

class Stripe extends AppConfiguration{
    	

    public function __construct(){
    	$this->type = "stripe";
    }

	public function getAuthUrl($state){
		return "https://connect.stripe.com/oauth/authorize?state=" . $state . "&response_type=code&client_id=" . Config::get('integration.stripe.client_id') . "&scope=read_write&redirect_uri=" . urlencode( ( strpos( env('API_PATH'), 'smartmember.dev' ) === false ? str_replace( 'http://','https://', env('API_PATH') ) : env('API_PATH') ) . '/stripe/auth' );
	}

    public static function getAccessToken($code){
    	$response = 
	    	Curl::get(
				"https://connect.stripe.com/oauth/token",
				array(
		    	    'grant_type' => 'authorization_code',
		    	    'client_id' => Config::get('integration.stripe.client_id'),
		    	    'code' => $code,
		    	    'client_secret' => Config::get('integration.stripe.secret_key')
		    	  )
			);
        //dd($response);
    	if (isset($response["error"])){
    		return false;
    	}

    	return $response;
    }

    public function integrate($data , $subdomain=''){
		$site = Site::whereSubdomain($subdomain)->first();
		if(!isset($site)){
			\App::abort('403' , 'No site selected');
		}
		$new_data = array();
		$new_data['type'] = 'stripe';
		$new_data['site_id'] = $site->id;
		$new_data['access_token'] = $data['access_token'];
		$new_data['remote_id'] = $data['stripe_user_id'];
		$new_data['name'] = $data['name'];

		$account = ConnectedAccount::firstOrCreate($new_data);
		$account->save();

		return $account;
    }

    public static function processSubscription($data){
		$site = Site::find( $data['site_id'] );

		//check to see if this access level was set to use a specific integration and grab it if it was
		if( $data['stripe_integration'] )
		{
			//checking site ids and company id just to make sure the integration belongs to the same company as the e-mail
			$site_ids = [$site->id];

			$app_configuration_instance = AppConfiguration::with('account')->whereId( $data['stripe_integration'] )->where(function($q) use ($site_ids){
				$q->whereIn('site_id',$site_ids);
			})->whereType('stripe')->whereDisabled(0)->first();
		}

		//if we didn't have an integration let's grab the default one for the site
		if( empty( $app_configuration_instance ) )
			$app_configuration_instance = AppConfiguration::with('account')->whereSiteId($site->id)->whereType('stripe')->whereDisabled(0)->orderBy('default','desc')->first();

        if(!$app_configuration_instance)
            \App::abort('403' , 'No stripe account connected');

        \Stripe\Stripe::setApiKey(Config::get('integration.stripe.secret_key'));
        return \Stripe\Customer::create(array(
          'plan' => $data['plan_id'],
          'description' => $data['product_id'],
          'email' => $data['email'],
          'source' => $data['token']
        ), array('stripe_account' => $app_configuration_instance->account->remote_id));
    }

    public static function processPayment($data){
		$site = Site::find( $data['site_id'] );

		//check to see if this access level was set to use a specific integration and grab it if it was
		if( $data['stripe_integration'] )
		{
			//checking site ids and company id just to make sure the integration belongs to the same company as the e-mail
			$site_ids = [ $site->id ];

			$app_configuration_instance = AppConfiguration::with('account')->whereId( $data['stripe_integration'] )->where(function($q) use ($site_ids){
				$q->whereIn('site_id',$site_ids);
			})->whereType('stripe')->whereDisabled(0)->first();
		}

		//if we didn't have an integration let's grab the default one for the site
		if( empty( $app_configuration_instance ) )
			$app_configuration_instance = AppConfiguration::with('account')->whereSiteId($site->id)->whereType('stripe')->whereDisabled(0)->orderBy('default','desc')->first();

        if(!$app_configuration_instance)
            \App::abort('403' , 'No stripe account connected');

        \Stripe\Stripe::setApiKey(Config::get('integration.stripe.secret_key'));
        return \Stripe\Charge::create(array(
              'amount' => $data['amount'],
              'currency' => $data['currency'],
              'source' => $data['token']
        ), array('stripe_account' => $app_configuration_instance->account->remote_id));
    }
}

?>