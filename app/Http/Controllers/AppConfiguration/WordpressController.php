<?php namespace App\Http\Controllers\AppConfiguration;

use App\Http\Controllers\AppConfigurationController;

use App\Models\AppConfiguration\Wordpress;

class WordpressController extends AppConfigurationController {

    public function __construct(){
        $this->model = new Wordpress();
        parent::__construct();
    }

    public function show(){
        return "I think this might work";
    }

    public function listPostTypes( $id ){
        return $this->model->listPostTypes( $this->site->id, $id );
    }

    public function listPosts( $id, $post_type = 'post' ){
        return $this->model->listPosts( $this->site->id, $id, $post_type );
    }

    public function store(){
        if( $this->model->checkCredentials( $_POST['connector_key'], $_POST['endpoint_url'] ) ) {
            $data = array(
                'connector_key' => $_POST['connector_key'],
                'endpoint_url' => $_POST['endpoint_url'],
                'site_id' => $this->site->id
            );

            $this->model->integrate( $data );
            return "success";
        } else {
            return "failure";
        }
    }
    
    
}
