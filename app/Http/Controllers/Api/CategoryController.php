<?php namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\User;

class CategoryController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Category();
        $this->middleware('admin',['except'=>array('index','show')]); 
               
    }

}