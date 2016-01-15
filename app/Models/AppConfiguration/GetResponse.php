<?php namespace App\Models\AppConfiguration;

use Curl;
use Config;
use App\Models\AppConfiguration;
use App\Helpers\jsonRPCClient;

class GetResponse extends AppConfiguration{
    public function __construct(){
        $this->type = "getresponse";
    }

    public static function getEmailList($app_configuration_instances)
    {
        $data = array();
        $data_return = array();
        $client = new jsonRPCClient('http://api2.getresponse.com');
        foreach ($app_configuration_instances as $app_configuration_instance)
        {
            $data = array_merge($data, $client->get_campaigns($app_configuration_instance->remote_id));
        }

        foreach ($data as $key => $value)
        {
            $data_return[] = array('id' => 'getresponse_' . $key, 'name' => $value['name']);
        }

        return $data_return;
    }

    public static function addMemberToList($list_id, $subscriber, $key)
    {
        $list_id = str_replace("getresponse_", "", $list_id);

        $client = new jsonRPCClient('http://api2.getresponse.com');
        $result = $client->add_contact(
            $key,
            array(
                'campaign' => $list_id,
                'name' => $subscriber->name,
                'email' => $subscriber->email
            )
        );

        \Log::info("Added subscriber to getresponse for " . $list_id . json_encode($result));
    }

}

?>