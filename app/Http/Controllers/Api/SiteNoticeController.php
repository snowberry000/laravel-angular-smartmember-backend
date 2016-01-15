<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\SiteNotice;
use App\Models\SiteNoticeSeen;
use App\Helpers\SMAuthenticate;
use Carbon\Carbon;

class SiteNoticeController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new SiteNotice();

        $this->middleware('admin',['except'=>array('index','show', 'getnotifications','getPrimaryAdminNotices')]); 
        $this->middleware("auth", ['except' => array('getnotifications')]);
    }

    public function getlist()
    {
        $site_id = $this->site->id;
        return $this->model->whereSiteId($site_id)->orderBy("created_at", "DESC")->get();
    }

    public function getnotifications()
    {   
        if (!$this->site){
            return [];
        }

        $seenSites=array();
        SMAuthenticate::set();
        date_default_timezone_set("UTC");
        $notice = null;
        $lessonNotice = null;

        if(\Auth::user()!= null)
        {
            $seenSites = SiteNoticeSeen::where('user_id' ,'=', \Auth::user()->id)->get(array('site_notice_id'));
            $seenSites=array_pluck($seenSites,'site_notice_id');
            
            $site_id = $this->site->id;

            if($this->model->whereSiteId($site_id)->whereOn(true)->count()>0)
            {
                $notice = $this->model->whereSiteId($site_id)->whereNotIn('id',$seenSites)->whereType('admin')->whereOn(true)->orderBy("created_at", "DESC")->first();
                $lessonNotice = $this->model->whereSiteId($site_id)->whereType('lesson')->whereNotIn('id',$seenSites)->orderBy("created_at", "DESC")->get();
                if($notice!=null)
                    $lessonNotice[]=$notice;
                return $lessonNotice;
            }

            $notice = $this->model->whereSiteId($site_id)->whereNotIn('id',$seenSites)->where('start_date','<=',date("Y-m-d H:i:s", time()))->where('end_date','>=',date("Y-m-d H:i:s", time()))->orderBy("start_date", "DESC")->first();
            $lessonNotice = $this->model->whereSiteId($site_id)->whereType('lesson')->whereNotIn('id',$seenSites)->orderBy("created_at", "DESC")->get();   
            //\Log::info($lessonNotice);
            if($notice!=null)
                $lessonNotice[]=$notice;
        } 
        else
        {
            $site_id = $this->site->id;

            if($this->model->whereSiteId($site_id)->whereOn(true)->count()>0)
            {
                $notice = $this->model->whereSiteId($site_id)->whereOn(true)->orderBy("created_at", "DESC")->first();
                $lessonNotice = $this->model->whereSiteId($site_id)->whereType('lesson')->orderBy("created_at", "DESC")->get();   
                if($notice!=null)
                    $lessonNotice[]=$notice;
                return $lessonNotice;
            }

            $notice = $this->model->whereSiteId($site_id)->where('start_date','<=',date("Y-m-d H:i:s", time()))->where('end_date','>=',date("Y-m-d H:i:s", time()))->orderBy("start_date", "DESC")->first();
            $lessonNotice = $this->model->whereSiteId($site_id)->whereType('lesson')->orderBy("created_at", "DESC")->get();   
            //\Log::info($lessonNotice);
            if($notice!=null)
                $lessonNotice[]=$notice;
        }

        return $lessonNotice;
    }

    public function getPrimaryAdminNotices(){

        if (!$this->site || $this->site->locked){
            return [];
        }

        if(!\SMRole::hasAccess($this->site->id,'manage_content'))
            return [];
        $seenSites=array();
        SMAuthenticate::set();
        date_default_timezone_set("UTC");
        $notice = null;

        if(\Auth::user()!= null)
        {
            $seenSites = SiteNoticeSeen::where('user_id' ,'=', \Auth::user()->id)->get(array('site_notice_id'));
            $seenSites=array_pluck($seenSites,'site_notice_id');
            $site_id = $this->site->id;
            $notice = $this->model->whereType('primary_admin_notices')->whereNotIn('id',$seenSites)->where('start_date','<=',date("Y-m-d H:i:s", time()))->where('end_date','>=',date("Y-m-d H:i:s", time()))->orderBy("start_date", "DESC")->whereOn(true)->first();
            return [$notice];
        }
        return [];
    }

    public function store()
    {
        $primary_admin__site=\Config::get('vars.admin_notices_site_id');
        $site_id = $this->site->id;
        if($primary_admin__site==$site_id)
        {
            $tempType=\Input::get('type');
            if($tempType!='lesson')
            {
                \Input::merge(array('type' => 'primary_admin_notices'));
            }
        }

        $this->model->site_id = $site_id;

        $onNotices = $this->model->whereSiteId($site_id)->whereType('admin')->where('start_date','<=',date("Y-m-d H:i:s", time()))->where('end_date','>=',\Input::get('end_date'))->orderBy("created_at", "DESC")->count();
        $current = Carbon::now();

        \Log::info($this->model->site_id);

        if(\Input::get('on')==true)
        {
            
            \DB::table('site_notices')->where('site_id', $site_id)->where('type', 'admin')->update(['on' => false]);
            \Log::info($this->model->site_id);
            $record = $this->model->create(array_merge(\Input::all(),array('site_id' => $this->model->site_id)));
            if (!$record->id){
                App::abort(401, "The operation requested couldn't be completed");
            }
            return $record;
        }

        if($onNotices>0&&($current->gte(new Carbon(\Input::get('start_date')))&&($current->lt(new Carbon(\Input::get('end_date'))))))
            \App::abort(403,"Some other notification is active in this period Only one notification can be 'On' at a time");
        else
        {
            \Log::info('h'.$this->model->site_id);
            $record = $this->model->create(array_merge(\Input::all(),array('site_id' => $this->model->site_id)));
            if (!$record->id){
                App::abort(401, "The operation requested couldn't be completed");
            }
            return $record;
        }
    }


    public function update($model){

        $site_id = $this->site->id;

        if(\Input::get('on')==true)
        {
            \DB::table('site_notices')->where('site_id', $site_id)->where('type','admin')->update(['on' => false]);
            $model->fill(\Input::except('_method'));
            $model->save();
            return $model;
        }

        $onNotices = $this->model->whereSiteId($site_id)->whereType('admin')->where('id','!=',$model->id)->where('start_date','<=',date("Y-m-d H:i:s", time()))->where('end_date','>=',date("Y-m-d H:i:s", time()))->orderBy("created_at", "DESC")->count();
        $current = Carbon::now();
        if($onNotices>0&&($current->gte(new Carbon(\Input::get('start_date')))&&($current->lt(new Carbon(\Input::get('end_date'))))))
            \App::abort(403,"Some other notification is active in this period Only one notification can be 'On' at a time");
        else

        $model->fill(\Input::except('_method'));
        $model->save();
        return $model;
    }

    public function index()
    {
        return parent::paginateIndex();
    }
    
}
