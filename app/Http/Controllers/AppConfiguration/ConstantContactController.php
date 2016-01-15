<?php namespace App\Http\Controllers\AppConfiguration;

use App\Http\Controllers\AppConfigurationController;
use App\Models\AppConfiguration;
use App\Models\AppConfiguration\ConstantContact;
use Input;
use App\Models\Site;


class ConstantContactController extends AppConfigurationController
{

    public function __construct(){
        $this->model = new ConstantContact();
        parent::__construct();
    }

    public function getAuth($id='')
	{
		$auth_url = $this->model->getAuthURL($id);

		if (Input::has('code')){
			if (!isset($id) || empty($id)) {
				$id = Input::get('app_configuration_id');
			}

			$response = $this->model->getAccessToken(Input::get('code'), $id);
			$connected_account = $this->model->integrate($response);

			$app_configuration_instance = AppConfiguration::find( $id );
			$app_configuration_instance->connected_account_id = $connected_account->id;
			$app_configuration_instance->save();

			header('Location: ' . \Domain::appRoute('my',"/my/integration/constantcontact/configure/" . $id));
			exit;
		} else {
			header('Location: ' . $auth_url);
			exit;
		}

    }
    
}
