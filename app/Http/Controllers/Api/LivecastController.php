<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Livecast;
use App\Models\Permalink;


class LivecastController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        //TODO: check if lesson is free
        $this->middleware("auth", ['except' => array('getByPermalink','index','show')]);
        $this->middleware('access' , ['only'=>array('show' , 'getByPermalink')]);
        $this->middleware('admin',['except'=>array('show' , 'index', 'getByPermalink')]); 
        
        $this->model = new Livecast();
    }

	public function index()
	{
		return parent::paginateIndex();
	}

    public function store()
    {
        $stored = parent::store();

        return $stored;
    }

    public function show($model){
        if($model->discussion_settings_id == 0){
            $this->model->addDiscussionSettings($model);
        }
        
        $model = $this->model->with("seo_settings","discussion_settings","access_level","dripfeed")->whereId($model->id)->first();
        return $model;
    }

    public function update($model){
        return $model->update(\Input::except('_method' , 'access'));
    }

	public function destroy($model)
	{
		$permalinks = Permalink::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->get();
		foreach( $permalinks as $permalink )
			$permalink->delete();

		return parent::destroy($model);
	}

    public function getByPermalink($id){
		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

        $livecast = Livecast::wherePermalink($id)->whereSiteId($this->site->id)->first();
        if($livecast)
            return $this->show($livecast);
        \App::abort('404','Livecast not found');
    }
}