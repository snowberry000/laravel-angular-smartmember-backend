<?php namespace App\Http\Controllers\AppConfiguration;

use App\Helpers\AweberHelper;
use App\Http\Controllers\AppConfigurationController;
use App\Models\UserOptions;
use App\Models\AppConfiguration;

use App\Models\AppConfiguration\AweberAppConfiguration;
use Input;

class AweberController extends AppConfigurationController
{

    public function __construct(){
        $this->model = new AweberAppConfiguration();
        parent::__construct();
    }

    public function getAuth($id='') {
		list($key, $secret) = $this->model->getAccessToken();
		$auth_url = $this->model->getAuthURL();

		$_SESSION['aweber_secret'] = $secret;
		if (Input::has('oauth_token') ) {
			$connected_account = $this->model->integrate(\Input::all());

			if (!isset($id)) {
				$id = Input::get('app_configuration_id');
			}

			$app_configuration_instance = AppConfiguration::find( $id );
			$app_configuration_instance->connected_account_id = $connected_account->id;
			$app_configuration_instance->save();
			header('Location: ' . \Domain::appRoute('my',"/my/integration/aweber/configure/" . $id));
			exit;
		} else {
			header('Location: ' . $auth_url);
			exit;
		}
    }
}
