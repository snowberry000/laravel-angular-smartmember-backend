<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Livecast;
use App\Models\Permalink;
use App\Models\ShortCode;


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

		\App\Models\Event::Log( 'created-livecast', array(
			'site_id' => $this->site->id,
			'user_id' => \Auth::user()->id,
			'livecast-title' => $stored->title,
			'livecast-id' => $stored->id
		) );

        return $stored;
    }

    public function show($model, $parse_shortcode = false){
        if($model->discussion_settings_id == 0){
            $this->model->addDiscussionSettings($model);
        }
        
        $model = $this->model->with("seo_settings","discussion_settings","access_level","dripfeed")->whereId($model->id)->first();
		if ($parse_shortcode)
			$model->content = ShortCode::replaceShortcode($model->content);
        return $model;
    }

    public function update($model){
        $stored = $model->update(\Input::except('_method' , 'access'));

		\App\Models\Event::Log( 'updated-livecast', array(
			'site_id' => $this->site->id,
			'user_id' => \Auth::user()->id,
			'livecast-title' => $stored->title,
			'livecast-id' => $stored->id
		) );

		return $stored;
    }

	public function destroy($model)
	{
		$permalinks = Permalink::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->get();
		foreach( $permalinks as $permalink )
			$permalink->delete();

		\App\Models\Event::Log( 'deleted-livecast', array(
			'site_id' => $this->site->id,
			'user_id' => \Auth::user()->id,
			'livecast-title' => $model->title,
			'livecast-id' => $model->id
		) );

		return parent::destroy($model);
	}

    public function getByPermalink($id){
		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

        $livecast = Livecast::wherePermalink($id)->whereSiteId($this->site->id)->first();
        if($livecast)
		{
			return $this->show($livecast, true);
		}

        \App::abort('404','Livecast not found');
    }
}