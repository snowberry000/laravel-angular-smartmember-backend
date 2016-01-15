<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\MetaDataType;

class MetaDataTypeController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new MetaDataType();
        $this->middleware('admin',['except'=>array('index','show')]); 
    }
}