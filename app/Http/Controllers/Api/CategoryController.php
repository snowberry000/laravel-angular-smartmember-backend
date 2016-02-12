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

	public function index()
	{
		if( \Input::has('view') && \Input::get('view') == 'admin' )
		{
			return parent::paginateIndex();
		}

		return $this->model->whereSiteId( $this->site->id )->get();
	}

	public function store()
	{
		\Input::merge(['site_id' => $this->site->id ]);
		return parent::store();
	}
}