<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\SiteTemplateData;

class SiteTemplateDataController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new SiteTemplateData();
        $this->middleware('admin',['except'=>array('index','show')]); 
    }
}