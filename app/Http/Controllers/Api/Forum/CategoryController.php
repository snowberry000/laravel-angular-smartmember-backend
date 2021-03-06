<?php namespace App\Http\Controllers\Api\Forum;

use App\Models\User;
use App\Models\AccessLevel;
use App\Models\Forum\Category;

use App\Http\Controllers\Api\SMController;

use Input;
use App\Helpers\SMAuthenticate;

class CategoryController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Category();

        $this->middleware("auth", ['only' => ['store','destroy','update'] ]); 
        $this->middleware("access", ['only' => ['getByPermalink'] ]); 
    }

    public function index(){

        Input::merge(['site_id'=>$this->site->id]);

        $roles = SMAuthenticate::getSiteRole($this->site->id);

        if (in_array("admin", $roles) || in_array("owner", $roles)){
            return parent::index();
        }else{
            $this->model = Category::whereIn('access_level_type',[1,2,3]);
        }

        return parent::index();
    }

    public function getByPermalink()
    {

    	$permalink = Input::get('permalink');
    	$query = Category::with(['topics.user','topics.replies'  => function($query) {
            $query->orderBy('created_at', 'desc'); }
        ]);
    	return $query->wherePermalink($permalink)->whereSiteId($this->site->id)->first();
    }

}