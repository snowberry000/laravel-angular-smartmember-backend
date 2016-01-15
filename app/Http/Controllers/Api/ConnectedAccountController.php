<?php namespace App\Http\Controllers\Api;

use App\Models\ConnectedAccount;
use App\Models\AppConfiguration;
use Auth;
use Input;



/*
    TODO: THis whole controller needs to be refactored. 
*/
class ConnectedAccountController extends SMController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware("auth",['except' => array('index','show')]);
        $this->middleware('access' , ['only'=>array('show' , 'single')]);
        $this->middleware('admin',['except'=>array('index','show')]);
        $this->model = new ConnectedAccount();
    }

    public function index()
    {
		$accounts = $this->model->whereAccountId( \Auth::user()->id )->with('app_configurations')->get();

		return $accounts;
    }

    public function show($model)
    {
       return parent::show($model);
    }

    public function store()
    {        
        $stored = $this->model->create( \Input::all() );

		$stored = $stored->with('app_configurations')->first();

        return $stored;
    }

    public function update($model)
    {
        $account = $model->update(\Input::except('_method' , 'access'));

		return array( 'success' => $account );
    }

    public function destroy($model){
		$account_id = $model->id;
        $account = parent::destroy($model);

		if( $account )
		{
			$app_configurations = AppConfiguration::whereConnectedAccountId( $account_id )->get();

			foreach( $app_configurations as $app_configuration )
			{
				$app_configuration->connected_account_id = 0;
				$app_configuration->disabled = 1;
				$app_configuration->save();
			}
		}

		return array( 'success' => $account );
    }

    public function single($id){

        return $this->model->whereId($id)->first();
    }

}