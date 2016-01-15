<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\SeoSetting;

class SeoSettingController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new SeoSetting();
        $this->middleware('admin',['except'=>array('index','show')]); 
    }
}