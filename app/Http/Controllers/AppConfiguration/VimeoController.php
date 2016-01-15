<?php namespace App\Http\Controllers\AppConfiguration;

use App\Helpers\VimeoHelper;
use App\Http\Controllers\AppConfigurationController;
use App\Models\VimeoIntegration;
use App\Models\UserOptions;
use App\Models\AppConfiguration;
use App\Models\Site;
use Input;
use App\Models\AppConfiguration\Vimeo;

class VimeoController extends AppConfigurationController
{

    public function __construct(){
        $this->model = new Vimeo();
        parent::__construct();
    }

    public function getAuth($id=''){
		$site_id = 0;
		$state = Input::get('state');
        $state = explode(':' , $state);
        $user_id = $state[0];
        $subdomain = $state[1];
		\Log::info($user_id.'I am userid');    		
		if( $id )
		{
			
			$current_options = UserOptions::whereUserId( $user_id)->whereMetaKey('vimeo_integration')->get();

			foreach( $current_options as $current_option )
				$current_option->delete();

			$new_option = UserOptions::create(['user_id'=>$user_id,'meta_key'=>'vimeo_integration','meta_value'=>$id]);
		}

    	$response = parent::getAuth($id);
    	if ($response["status"] == 400){
            \App::abort(401,"Something went wrong, please try again later");
        }

		$data = $this->model->integrate($response , $subdomain , $user_id);

		$current_option = UserOptions::whereUserId( $user_id )->whereMetaKey('vimeo_integration')->first();

		$app_configuration_instance_id = '';

		if( $current_option )
		{
			$app_configuration_instance_id = $current_option->meta_value;
			$current_option->delete();

			$app_configuration_instance = AppConfiguration::find( $app_configuration_instance_id );
			$app_configuration_instance->connected_account_id = $data->id;
			$site_id = $app_configuration_instance->site_id;
			$app_configuration_instance->save();
		}
		$site = Site::whereSubdomain($subdomain)->first();
		if (empty($site->domain))
		{
			$redirect_url = \Domain::appRoute($site->subdomain,'');
		} else {
			$redirect_url = 'http://' . $site->domain;
		}
		$redirect_url =  $redirect_url . '?vimeo';
    	header('Location: ' . $redirect_url);
        exit;
    }
    
    
}
