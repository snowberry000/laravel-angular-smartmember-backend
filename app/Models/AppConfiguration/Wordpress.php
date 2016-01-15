<?php namespace App\Models\AppConfiguration;

use Curl;
use App\Models\AppConfiguration;
use Config;

class Wordpress extends AppConfiguration {
    
    protected $library;

    public function __construct(){
    	$this->type = "wordpress";
    }

    public function checkCredentials( $key, $endpoint ) {
        $response = $this->talkToWordpress( array( 'endpoint' => $endpoint, 'key' =>$key ), 'check_credentials' );

        return !empty( $response['value'] ) && $response['value'] == true ? true : false;
    }

    public function integrate( $data ){
    	return parent::integrate( array(
    			"access_token" => $data["connector_key"],
    			"remote_id" => $data["endpoint_url"],
    			"site_id" => $data["site_id"]
    		) );
    }

    public function getWordpressCredentials( $site_id, $id ){
        $app_configuration_instance = AppConfiguration::whereSiteId( $site_id )->whereId( $id )->whereType('wordpress')->first();

        if(!$app_configuration_instance)
            \App::abort('403' , 'You don\'t have access to that site.');
        else
            return array( 'key' => $app_configuration_instance->access_token, 'endpoint' => $app_configuration_instance->remote_id );
    }

    public function listPostTypes( $site_id, $id ){
        return $this->requestData( $site_id, $id, 'list_post_types' );
    }

    public function listPosts( $site_id, $id, $post_type = 'post' ){
        return $this->requestData( $site_id, $id, 'list_posts', array( 'post_type' => $post_type ) );
    }

    public static function mapToLesson($data){
        $lesson = array(
            "title" => $data["post_title"],
            "featured_image" => $data["thumbnail"],
            "remote_id" => $data["ID"],
            "permalink" => $data['post_name'],
            "type" => "wordpress"
        );

        $lesson["content"] = isset( $data["post_content"] ) ? $data["post_content"] : $data["title"];

        return $lesson;
    }

    public function requestData( $site_id, $id, $function_name, $extra_data = array() ) {
        $wordpress_site = $this->getWordpressCredentials( $site_id, $id );

        return $this->talkToWordpress( $wordpress_site, $function_name, $extra_data );
    }

    private function talkToWordpress( $credentials, $function_name, $extra_data = array() ){
        return Curl::get(
            $credentials['endpoint'],
            array_merge(
                array(
                    'smc_hash' => $credentials['key'],
                    'function' => $function_name
                ),
                $extra_data
            )
        );
    }
}

?>