<?php namespace App\Http\Controllers\Api;

use App\Models\SiteNoticeSeen;
use App\Models\User;
use App\Helpers\SMAuthenticate;

class SiteNoticeSeenController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new SiteNoticeSeen();  
        $this->middleware("auth",['except' => array('index','show','store')]);   
    }

    public function store()
    {
    	SMAuthenticate::set();
    	if(\Auth::user()!=null)
    		SiteNoticeSeen::create(array_merge(\Input::all(),array('user_id' => \Auth::user()->id)));
    }
}