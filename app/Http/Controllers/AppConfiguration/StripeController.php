<?php

namespace App\Http\Controllers\AppConfiguration;

use App\Http\Controllers\AppConfigurationController;
use App\Models\AppConfiguration\Stripe;

use App\Models\Site;
use App\Models\AppConfiguration;
use App\Models\AccessLevel\Pass;
use App\Models\UserOptions;
use Input;
use Curl;
use App;
use Config;

class StripeController extends AppConfigurationController
{   
    public function __construct(){
        $this->model = new Stripe();
        parent::__construct();
    }

    public function getAuth($id=''){
        $site_id = 0;
        $state = Input::get('state');
        $state = explode(':' , $state);
        $user_id = $state[0];
        $subdomain = $state[1];
		if( $id )
		{
			$current_options = UserOptions::whereUserId( $user_id )->whereMetaKey('stripe_integration')->get();

			foreach( $current_options as $current_option )
				$current_option->delete();

			$new_option = UserOptions::create(['user_id'=>$user_id,'meta_key'=>'stripe_integration','meta_value'=>$id]);
		}

        $response = parent::getAuth($id);
        if (!isset($response["access_token"])){
            if( !empty( $response['error'] ) && !empty( $response['error_description'] ) )
            {
                header( 'Location: ' . \Domain::appRoute( 'my', "/my/integrations/all" ) );
                exit;
            }
            else
                \App::abort(401,"Something went wrong, please try again later");
        }
        \Stripe\Stripe::setApiKey(Config::get('integration.stripe.secret_key'));
        $account = \Stripe\Account::retrieve($response['stripe_user_id']);
        if($account){
            $response['name'] = $account['email'];
        }
        $data = $this->model->integrate($response , $subdomain);

		$current_option = UserOptions::whereUserId( $user_id )->whereMetaKey('stripe_integration')->first();

		$app_configuration_instance_id = '';

		if( $current_option )
		{
			$app_configuration_instance_id = $current_option->meta_value;
			$current_option->delete();

			$app_configuration_instance = AppConfiguration::find( $app_configuration_instance_id );
            $site_id = $app_configuration_instance->site_id;
			$app_configuration_instance->connected_account_id = $data->id;
			$app_configuration_instance->save();
		}
        $site = Site::whereSubdomain($subdomain)->first();
        if (empty($site->domain))
        {
            $redirect_url = \Domain::appRoute($site->subdomain,'');
        } else {
            $redirect_url = 'http://' . $site->domain;
        }

        $redirect_url =  $redirect_url . ( !empty( $app_configuration_instance_id ) ? '/admin/apps/integration/stripe/configure/' . $app_configuration_instance_id : '/admin/apps/app_configurations/list' );
        header('Location: ' . $redirect_url);
        exit;
    }

    public function postIpn(){
        $data = \Input::all();
        if( !empty( $data['type'] ) )
        {
            switch( $data['type'] )
            {
                case 'invoice.payment_succeeded':
                    if( !empty( $data[ 'data' ][ 'object' ][ 'subscription' ] ) && !empty( $data[ 'data' ][ 'object' ][ 'lines' ][ 'data' ][0][ 'period' ][ 'end' ] ) )
                    {
                        $subscription_id = $data[ 'data' ][ 'object' ][ 'subscription' ];
                        $expiration      = $data[ 'data' ][ 'object' ][ 'lines' ][ 'data' ][ 0 ][ 'period' ][ 'end' ];
                        $access_pass     = Pass::whereSubscriptionId( $subscription_id )->first();
                        if( $access_pass )
                        {
                            return Pass::updatePass( $access_pass, $expiration );
                        }
                    }
                    break;
                default:
            }
        }

        return $data;
    }

}
