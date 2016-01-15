<?php

namespace App\Http\Controllers;

use App\Helpers\SMAuthenticate;
use Input;
use App\Models\Site;
use App\Models\ConnectedAccount;

class AppConfigurationController extends Controller
{
    protected $model;
    protected $site;

    public function __construct(){
		if( Input::has('state') )
		{
			Input::merge( array('access_token'=>Input::get('state') ) );
		} else if (Input::has('oauth_token'))
        {
            Input::merge(array('access_token'=>Input::get('oauth_token')));
        } else if (Input::has('code'))
        {
            Input::merge(array('access_token'=>Input::get('code')));
        }
		else
		{
			$this->site = Site::whereSubdomain(\Domain::getSubdomain())->first();
		}

		$this->middleware("auth");
    }

    public function getAuth($id=''){
        $auth_url = $this->model->getAuthURL(Input::get('state'));

        if (Input::has('code')){
            $response = $this->model->getAccessToken(Input::get('code'));
            return $response;
        }

        if ( Input::has('error') && Input::get('error') != '' ){
            $response = array('error' => Input::get('error'), 'error_description' => Input::get('error_description') );
            return $response;
        }



        header('Location: ' . $auth_url);
        exit;
    }

}
