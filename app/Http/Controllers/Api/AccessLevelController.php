<?php namespace App\Http\Controllers\Api;

use App\Models\Site;
use App\Models\AccessLevel;
use Input;
use App\Models\AccessLevel\Pass;

class AccessLevelController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new AccessLevel();   
        $this->middleware('auth',['except'=>array('index','show')]); 
        $this->middleware('admin',['except'=>array('index','show')]);    
    }

    public function show($model){
    	$model->grants = $model->grants;
        $model->shared_grants = $model->shared_grants;
        $model->payment_methods = $model->paymentMethods;
    	return parent::show($model);
    }

	public function index()
	{
		if( Input::has('view') && Input::get('view') == 'admin' )
			return parent::paginateIndex();
		else
			return parent::index();
	}

    public function refreshHash()
    {
        AccessLevel::where('id','=',\Input::get('id'))->update(array('hash'=> md5(microtime())));
        return AccessLevel::where('id','=',\Input::get('id'))->first();
    }

    public function update($model){
        return $model->update(\Input::all());
    }

    public function destroy($model){
        return parent::destroy($model);
    }

    public function sendMailAccessLevels(){
        return $this->model->whereSiteId($this->site->id)->get();
    }

    public function lock(){
		if( \Input::get('access_level_type') == 2 )
        	$access_level = $this->model->create(\Input::except(['access_token', 'token' , 'send_email', 'access_level_type']));

        $tables = ["lessons" , "download_center" , "livecasts" , "custom_pages"];

		$update_data = [ 'access_level_type' => \Input::get('access_level_type') ];

		if( !empty( $access_level ) && isset($access_level->site_id) )
		{
			$update_data['access_level_id'] = $access_level->id;
		}
		else
		{
			$update_data['access_level_id'] = 0;
		}

		foreach ($tables as $key => $table)
		{
			\DB::table($table)
				->where('site_id' , \Input::get('site_id') )
				->update($update_data);
		}

        return !empty( $access_level ) ? $access_level : ['success'];
    }
}