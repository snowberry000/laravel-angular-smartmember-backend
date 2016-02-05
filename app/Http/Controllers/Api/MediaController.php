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
    	Input::merge(['site_id' => $this->site->id]);
    	return parent::paginateIndex();
    }

}