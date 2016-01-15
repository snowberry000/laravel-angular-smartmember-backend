<?php namespace App\Http\Controllers\Api;

use App\Models\SpecialPage;

class SpecialPageController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new SpecialPage();  
        
        $this->middleware('auth',['except'=>array('index','show')]); 
        $this->middleware('admin',['except'=>array('index','show')]);  
    }

    public function index2(){
        //return array('passes'=>$passes , 'total'=>Pass::whereSiteId(\Input::get('site_id'))->count());
    }

    public function show($model){
    	$model->access_level = $model->access_level;
    	return parent::show($model);
    }

    /*
		REFACTOR:
    */
    public function store(){
    	return SpecialPage::create(\Input::all());
    }

}