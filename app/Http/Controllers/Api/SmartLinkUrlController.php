<?php namespace App\Http\Controllers\Api;

use App\Models\Site;
use App\Models\SmartLinkUrl;
use App\Models\AccessLevel\Pass;

class SmartLinkUrlController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new SmartLinkUrl();
        $this->middleware('auth',['except'=>array('index','show')]); 
        $this->middleware('admin',['except'=>array('index','show')]);    
    }
}