<?php namespace App\Http\Controllers\Api;

use App\Models\Site;
use App\Models\AppConfiguration;
use App\Models\ConnectedAccount;
use App\Models\AppConfiguration\SendGridEmail;
use App\Models\AppConfiguration\Youzign;
use App\Models\AccessLevel;
use Auth;
use Input;

/*
    TODO: THis whole controller needs to be refactored.
*/
class AppConfigurationController extends SMController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware("auth",['except' => array('index','show')]);
        $this->middleware('access' , ['only'=>array('show' , 'single')]);
        $this->middleware('admin',['except'=>array('index','show')]);
        $this->model = new AppConfiguration();
    }

    public function index()
    {
        $site_id = $this->site->id;

		$accounts = $this->model->where(function($query) use ( $site_id ){
			$query->where('site_id', $site_id );
		})->with(['site','account','meta_data'])->whereIn('type',['facebook_group','sendgrid','vimeo','intercom','youzign'])->get();

		return $accounts;
    }

    public function show($model)
    {
       	$app_configuration_instance = $model->with(['site','account','meta_data'])->find($model->id);

		return $app_configuration_instance;
    }

    public function store()
    {
		if( $this->site )
			\Input::merge(['site_id' => $this->site->id ] );

        $stored = parent::store();

		if( $stored->type == 'sendgrid' )
		{
			$check = SendGridEmail::checkCredentials($stored->username, $stored->password );
			if (!$check)
				$stored->status = 'bad_credentials';
		}

		if ($stored->type == 'youzign')
		{
			$stored->access_token = \Input::get('access_token');
			$stored->save();
			$stored->status = YouZign::importAssets($stored->remote_id, $stored->access_token, $this->site->id);
		}

        return $stored;
    }

    public function update($model)
    {
        $account = $model->update(\Input::except('_method' , 'access'));
		if( $model->type == 'sendgrid' )
		{
			$check = SendGridEmail::checkCredentials($model->username, $model->password );

			if (!$check)
				return array( 'status' => 'bad_credentials' );

			if( $model->default )
			{
				$app_configuration_instances = $this->model->whereType( $model->type )->whereDefault(1)->where('id','!=',$model->id);
				if( $model->site_id )
					$app_configuration_instances = $app_configuration_instances->whereSiteId( $model->site_id );

				$app_configuration_instances = $app_configuration_instances->get();

				if( $app_configuration_instances )
				{
					foreach( $app_configuration_instances as $app_configuration_instance )
					{
						$app_configuration_instance->default = 0;
						$app_configuration_instance->save();
					}
				}
			}
		}

		return array( 'success' => $account );
    }

    public function uninstallApp(){
    	//return array('ids' => \Input::get('ids'));
    	$arr = [];
    	foreach (\Input::get('ids') as $key => $value) {
    		$currentModel = $this->model->whereId($value)->first();
    		if(!empty($currentModel))
    		{
	    		// $connectedAccount = ConnectedAccount::whereId($currentModel->connected_account_id)->first();
	    		// if(empty($connectedAccount))
	    		// 	$arr [] = 0;
	    		// else
	    		// {
	    		// 	$arr [] = $connectedAccount->id;
	    		// 	$connectedAccount->delete();
	    		// }
	    		$currentModel->delete();	
    		}
    	}
    	return $arr;
    }

    public function destroy($model){
		if ($model->type == 'facebook_group')
		{
			if ($model->site_id != '')
			{
				$access_levels = AccessLevel::whereSiteId($model->site_id)->where('facebook_group_id',
					$model->remote_id)->get();
				foreach ($access_levels as $access_level)
				{
					$access_level->facebook_group_id = '';
					$access_level->save();
				}
			}
		}
        $app_configuration_instance = parent::destroy($model);

		return array( 'success' => $app_configuration_instance );
    }

    public function single($id){

        return $this->model->whereId($id)->first();
    }

	public function getSendgridIntegrations(){
		$site_id = $this->site->id;

		$accounts = $this->model->where(function($query) use ( $site_id ){
			$query->where('site_id', $site_id );
		})->with(['site','account','meta_data'])->whereType('sendgrid')->whereDisabled(0)->select(['id','username','site_id','default'])->get();

		return $accounts;
	}

	public function getPaymentIntegrations(){
		$site_id = $this->site->id;

		$accounts = $this->model->where(function($query) use ( $site_id ){
			$query->where('site_id', $site_id );
		})->with(['site','account','meta_data'])->whereIn('type',['stripe','paypal'])->whereDisabled(0)->select(['id','username','site_id','default'])->get();

		$results = [
			'stripe' => [],
			'paypal' => []
		];

		foreach( $accounts as $account )
			$results[ $account->type ][] = $account;

		return $results;
	}

}