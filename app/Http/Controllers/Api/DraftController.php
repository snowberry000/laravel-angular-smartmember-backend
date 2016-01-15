<?php namespace App\Http\Controllers\Api;

use App\Models\Site;
use App\Models\Draft;
use App\Models\AccessLevel\Pass;

class DraftController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Draft();   
        $this->middleware('auth',['except'=>array('index','show')]); 
        $this->middleware('admin',['except'=>array('index','show')]);    
    }
}