<?php namespace App\Http\Controllers\AppConfiguration;

use App\Http\Controllers\AppConfigurationController;
use App\Models\UserOptions;
use App\Models\AppConfiguration;
use App\Helpers\jsonRPCClient;
use App\Models\AppConfiguration\GetResponse;
use Input;

class GetResponseController extends AppConfigurationController
{

    public function __construct(){
        $this->model = new GetResponse();
		$this->api_url = 'http://api2.getresponse.com';
		$this->client = new jsonRPCClient($this->api_url);
        parent::__construct();
    }

    public function getAuth($id='') {
		$response = parent::getAuth($id);
    }

	public function postStore()
	{
		$app_configuration_instance = AppConfiguration::whereType('getresponse')->whereSiteId($this->site->id)->first();
		if ($app_configuration_instance) {
			$app_configuration_instance->remote_id = Input::get('remote_id');
			$app_configuration_instance->save();
			return $app_configuration_instance;
		}

		return AppConfiguration::create(array(
				"remote_id" => Input::get('remote_id'),
				"type" => "getresponse",
				"site_id" => $this->site->id
		));
	}

	public function getCampaigns()
	{
		$return_arr = array();
		$app_configuration_instance_id = \Input::get('app_configuration_id');
		$app_configuration_instance = AppConfiguration::find($app_configuration_instance_id);

		$data = $this->client->get_campaigns($app_configuration_instance->remote_id);
		foreach ($data as $key => $value)
		{
			$return_arr[] = array('id' => 'getresponse_' . $key, 'name' => $value['name']);
		}

		return $return_arr;

	}
}
