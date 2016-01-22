<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Download;
use App\Models\Permalink;
use App\Models\DownloadHistory;
use App\Helpers\SMAuthenticate;
use Auth;
use input;

class DownloadController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new Download();
        $this->middleware("auth",['except' => array('index','show' ,'getDownload' ,'getByPermalink')]);
        $this->middleware('access' , ['only'=>array('show', 'single' ,'getDownload','getByPermalink')]);
        $this->middleware('admin',['except'=>array('index','show','getDownload' , 'getByPermalink')]); 

    }

    public function index(){
        if (isset($this->site->id))
            \Input::merge(array('site_id' => $this->site->id));
        $total_count=0;
        if( Input::has('view') && Input::get('view') == 'admin' )
        {
            $downloads = parent::paginateIndex();
            $total_count=$downloads['total_count'] ;
            $downloads= $downloads['items'];
        }
        else
        {
             $downloads = parent::index();
        }

        
        $downloads = array_values(array_sort($downloads, function($value)
        {
            return $value['sort_order'];
        }));

        $returnDownloads=[];
        $length = count($downloads);
        if($length==0)
            return;
        for ($i = 0 ; $i < $length ; $i++ ) {
            if($downloads[$i]->access_level_type==4){
                if (!\App\Helpers\SMAuthenticate::set() || !\SMRole::hasAccess($this->site->id,'manage_content') ){
                    unset($downloads[$i]);
                    continue;
                }
            }
            if($downloads[$i]->access_level_type==2){
                if(!SMAuthenticate::checkIfUnowned($downloads[$i])){
                    unset($downloads[$i]);
                    continue;
                }
            }
            if (!$downloads[$i]->preview_schedule && $downloads[$i]->published_date != '0000-00-00 00:00:00') {
                if (!SMAuthenticate::checkScheduleAvailability($downloads[$i])) {
                    unset($downloads[$i]);
                    continue;
                }
            }
            if (!$downloads[$i]->preview_dripfeed) {
                if (!SMAuthenticate::checkDripAvailability($downloads[$i])) {
                    unset($downloads[$i]);
                    continue;
                }
            }

            /*if($downloads[$i]->access_level_type == 3)
                $returnDownloads[] = $this->show($downloads[$i]);
            else if(!SMAuthenticate::checkAccessLevel($downloads[$i])){
                unset($downloads[$i]);
                continue;
            }
            else if($downloads[$i]->access_level_type==2){
                if(!SMAuthenticate::checkIfUnowned($downloads[$i])){
                    unset($downloads[$i]);
                }else{
                    $returnDownloads[] = $this->show($downloads[$i]);
                }
            }
            else
                $returnDownloads[] = $this->show($downloads[$i]);*/

            $downloads[$i] = $this->show($downloads[$i]);
        }

		$final_downloads = [];

		foreach( $downloads as $download )
			$final_downloads[] = $download;

        return array('total_count' => $total_count , 'items' => $final_downloads);
    }

    public function store()
    {        
        $stored = parent::store();
        return $stored;
    }

    public function getlist()
    {
        return $this->model->with("history_count", "unique_count")->whereSiteId($this->site->id)->get();
    }

    public function show($model){
        $model = $this->model->with(["seo_settings" , 'media_item' => function($query){ $query->whereSiteId( $this->site->id ); } , 'history_count',"dripfeed"])->whereId($model->id)->first();
        if(\App\Helpers\SMAuthenticate::set()){
            $model->user_count = DownloadHistory::whereDownloadId($model->id)->whereUserId(Auth::user()->id)->count();
        }
        return $model;
    }

    public function single($id)
    {
        return $this->model->with("seo_settings")->whereId($id)->first();
    }

    public function update($model)
    {
        return $model->update(\Input::except('_method' , 'access'));
    }

    public function putDownloads()
    {
        $input=\Input::get("downloads");
        //dd($input);
        foreach ($input as $key => $value) {
            Download::whereId($value['id'])->
            update(['sort_order' => $value['sort_order']]);
        }
    }

	public function destroy($model)
	{
		$permalinks = Permalink::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->get();
		foreach( $permalinks as $permalink )
			$permalink->delete();

		return parent::destroy($model);
	}

    public function getDownload($id)
    {
        $download = Download::with('history')->find($id);

        if(!SMAuthenticate::checkAccessLevel($download))
            \App::abort('403','You do not have access to this resource');

        $user_id = (\App\Helpers\SMAuthenticate::set()) ? Auth::user()->id : 0;
        $history = DownloadHistory::create(array('user_id'=> $user_id ));
        $download->history()->save($history);
        $download->save();
        if($download->media_item && $download->media_item->site_id == $download->site_id && $download->media_item->aws_key==""){
            $download->my_url = $download->media_item->url;
            return $download;
        }
        if($download->media_item && $download->media_item->site_id == $download->site_id){
            $download->aws_key = $download->media_item->aws_key;
            return $download;
        }

		\App::abort('404','Download link not found');
    }

    public function getByPermalink($id){
        $download = Download::wherePermalink($id)->whereSiteId($this->site->id)->first();
        if($download)
            return $this->show($download);
        \App::abort('404','Download not found');
    }
}
