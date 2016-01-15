<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\AffiliateJVPage;
use App\Models\User;
use App\Models\UserOptions;
use Auth;

class AffiliateJVPageController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new AffiliateJVPage();
        $this->middleware('auth',['except'=>array('show','index')]);
        $this->middleware('admin',['except'=>array('show','index')]);
    }

    public function index()
    {
		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

    	$this->model = $this->model->with('emailList');
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->take($page_size);
        $query = $query->orderBy('id' , 'DESC');
        $query = $query->whereSiteId($this->site->id);
        $query = $query->whereNull('deleted_at');
        foreach (\Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value,$this->site);
                    break;
                case 'p':
                    $query->skip((\Input::get('p')-1)*$page_size);
                    break;
                default:
            }
        }

        return $query->get();
    }

	public function store(){
		$record = $this->model->create(\Input::except(['access_token', 'token' , 'send_email']));

		$record->site_id = $this->site->id;
		$record->save();

		if (!$record->id){
			App::abort(401, "The operation requested couldn't be completed");
		}
		return $record;
	}

	public function update($model){
		$model->fill(\Input::except('_method' , 'send_email'));
		$model->site_id = $this->site->id;
		$model->save();
		return $model;
	}

}