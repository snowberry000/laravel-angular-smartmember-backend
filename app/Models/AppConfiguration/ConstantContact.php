<?php namespace App\Models\AppConfiguration;

use App\Models\AppConfiguration;
use App\Models\ConnectedAccount;
use Config;
use Curl;

class ConstantContact extends AppConfiguration {

    protected $library;

    public function __construct(){
        $this->type = "constantcontact";
        $this->api_key = \Config::get("integration.constantcontact.api_key");
        $this->consumer_secret = \Config::get("integration.constantcontact.consumer_secret");
    }

    public function getAuthURL($app_configuration_instance_id){
        $redirect_url = \Domain::apiPath('/constantcontact/auth') . '?app_configuration_id=' . $app_configuration_instance_id;
        return "https://oauth2.constantcontact.com/oauth2/oauth/siteowner/authorize?response_type=code&client_id=" . $this->api_key . "&redirect_uri=" . urlencode($redirect_url);
    }

    public function getAccessToken($code, $app_configuration_instance_id) {
        $redirect_url = \Domain::apiPath('/constantcontact/auth') . '?app_configuration_id=' . $app_configuration_instance_id;
        $data = Curl::post("https://oauth2.constantcontact.com/oauth2/oauth/token", array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->api_key,
            'client_secret' => $this->consumer_secret,
            'code' => $code,
            'redirect_uri' => $redirect_url
        ), array() );
        return $data;
    }

    public function integrate($data){
		$site = \Domain::getSite();
        $new_data = array();
        $new_data['type'] = 'constantcontact';
        $new_data['site_id'] = $site->id;
        $new_data['access_token'] = $data["access_token"];
        $new_data['remote_id'] = urldecode(\Input::get('username'));
        $account = ConnectedAccount::whereType('constantcontact')->whereSiteId($site->id)->whereAccessToken( $new_data['access_token'] )->first();
        if( $account )
            $account->access_token = $new_data['access_token'];
        else
            $account = ConnectedAccount::firstOrCreate($new_data);

        $account->save();

        return $account;
    }

    public static function getEmailList($app_configuration_instances)
    {
        $data = array();
        $data_return = array();

        foreach ($app_configuration_instances as $app_configuration_instance)
        {
            $access_token = $app_configuration_instance->account->access_token;

            $authorize_url = 'https://api.constantcontact.com/v2/lists';
            $data = Curl::actual_get($authorize_url, array('api_key' => \Config::get("integration.constantcontact.api_key")), array(
                'Authorization: Bearer '.$access_token
            ));

            foreach ($data as $key => $value)
            {
                $data_return[] = array('id' => 'constantcontact_' . $value['id'], 'name' => $value["name"]);
            }

            return $data_return;

        }
    }

    public static function createEmailListForSite($secret, $site)
    {
        $authorize_url = 'https://api.constantcontact.com/v2/lists?api_key=' . \Config::get("integration.constantcontact.api_key");
        $data = Curl::post($authorize_url, array('name' => $site->name, "status" => 'ACTIVE'), array(
            'Authorization: Bearer '.$secret,
            'Content-Type: application/json'
        ), true);
        return $data['id'];
    }

    public static function addMemberToList($list_id, $subscriber, $secret = '')
    {
        $list_id = str_replace("constantcontact_", "", $list_id);

        $authorize_url = 'https://api.constantcontact.com/v2/contacts?api_key=' . \Config::get("integration.constantcontact.api_key") . '&action_by=ACTION_BY_OWNER';
        $data = Curl::post($authorize_url, array(
            'lists' => array(0 => array('id' => $list_id)), 'email_addresses' => array(0 => array('email_address' => $subscriber->email)), 'first_name' => $subscriber->name
        ), array(
            'Authorization: Bearer '.$secret,
            'Content-Type: application/json'
        ), true);
        dd($data);
    }
}

?>