<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\UserOptions;
use App\Models\User;
use Input;

class UserOptionsController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new UserOptions();
    }


    public function index(){
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->take($page_size);
        $query = $query->orderBy('id' , 'DESC');
        $query = $query->whereNull('deleted_at');
        $query = $query->whereUserId(\Input::get('user_id'));
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


}