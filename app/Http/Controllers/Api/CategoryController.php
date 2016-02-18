<?php namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\User;
use App\Models\PostCategory;

class CategoryController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Category();
        $this->middleware('admin',['except'=>array('index','show')]);
		$this->middleware('auth', ['except'=>array('index')]);
    }

	public function index()
	{
		if( \Input::has('view') && \Input::get('view') == 'admin' )
		{
			$data = parent::paginateIndex();
			foreach ($data['items'] as $post_category)
			{
				$post_count = PostCategory::where('category_id', $post_category->id)->whereNull('deleted_at')->count();
				$post_category->post_count = $post_count;
			}
			return $data;
		}

		return $this->model->whereSiteId( $this->site->id )->get();
	}

	public function store()
	{
		\Input::merge(['site_id' => $this->site->id ]);
		return parent::store();
	}
}