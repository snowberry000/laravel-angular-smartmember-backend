<?php namespace App\Http\Controllers\Api;

use App\Models\Media;
use Input;

class MediaController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Media();   
    }

    public function index(){
    	if(!empty($this->site->id))
    	{
    		Input::merge(['site_id' => $this->site->id]);
    		return parent::paginateIndex();
    	}
    	return ['items' => [] , 'total_count' => 0];
    	
    }

}