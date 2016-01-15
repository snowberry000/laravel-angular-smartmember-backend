<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\SiteMenuItem;
use Input;

class SiteMenuItemController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new SiteMenuItem();
        $this->middleware('admin',['except'=>array('index','show')]); 
    }

    public function index(){
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->take($page_size);
        $query = $query->whereNull('deleted_at');
        foreach (Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
                case 'p':
                    $query->skip((Input::get('p')-1)*$page_size);
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }
        return $query->get();
    }

    public function update($model){
        $model->fill(Input::except('_method' , 'type'));
        $model->save();
        return $model;
    }

    public function store()
    {
        //TODO: check if admin
        $site_id = $this->site->id;
        \Input::merge(array('site_id' => $site_id));
        return parent::store();
    }
}