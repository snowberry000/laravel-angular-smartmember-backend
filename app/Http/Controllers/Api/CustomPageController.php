<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\CustomPage;
use App\Models\Permalink;

class CustomPageController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth' , ['except'=>array('index','show' , 'single' ,'getByPermalink')]);
        $this->middleware('access' , ['only'=>array('show' , 'single' ,'getByPermalink')]);
        $this->middleware('admin',['except'=>array('index','show' ,  'getByPermalink')]); 
        $this->model = new CustomPage();
    }

    public function index()
    {
        \Input::merge(array('site_id' => $this->site->id));
        $index = parent::paginateIndex();
        return $index;
    }

    public function getlist()
    {
        return $this->model->whereSiteId($this->site->id)->get();
    }

    public function show($model){

        if($model->discussion_settings_id == 0){
            $this->model->addDiscussionSettings($model);
        }

        $model = $this->model->with("seo_settings","discussion_settings","access_level")->whereId($model->id)->first();
        return $model;
    }

    public function single($id)
    {
        return $this->model->with("seo_settings","discussion_settings")->whereId($id)->first();
    }

    public function store()
    {
        $stored = parent::store();

        return $stored;
    }

	public function destroy($model)
	{
		$permalinks = Permalink::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->get();
		foreach( $permalinks as $permalink )
			$permalink->delete();

		return parent::destroy($model);
	}

    public function update($model){
        return $model->update(\Input::except('_method' , 'access'));
    }

    public function getByPermalink($id){
		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

        $page = CustomPage::wherePermalink($id)->whereSiteId($this->site->id)->first();
        if($page)
            return $this->show($page);
        \App::abort('404','Page not found');
    }
}