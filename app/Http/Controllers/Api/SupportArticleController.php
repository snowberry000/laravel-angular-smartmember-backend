<?php namespace App\Http\Controllers\Api;

use App\Models\SupportArticle;

class SupportArticleController extends SMController
{
	public function __construct()
	{
		parent::__construct();

		$this->middleware( "auth", [ 'except' => array( 'index', 'show', 'getByPermalink' ) ] );
		$this->middleware( 'admin', [ 'except' => array( 'index', 'show', 'getByPermalink' ) ] );
		$this->model = new SupportArticle();
	}

	public function store()
	{
		\Input::merge( array( 'author_id' => \Auth::user()->id, 'site_id' => $this->site->id ) );
		return parent::store();
	}

	public function update( $model )
	{
		\Input::merge( array( 'site_id' => $this->site->id ) );
		return $model->update( \Input::all() );
	}

	public function show( $model )
	{
		if( $model->parent_id != 0 )
			$model->parent = $this->show( $this->model->find( $model->parent_id ) );

		return $model;
	}

	public function index()
	{
		\Input::merge( array( 'site_id' => $this->site->id, 'bypass_paging' => true, 'p' => null ) );
		$data = parent::paginateIndex();

		foreach( $data['items'] as $key => $val )
		{
			if( $val->parent_id != 0 )
				$val->parent = $this->show( $this->model->find( $val->parent_id ) );
		}

		return $data;
	}

	public function getByPermalink( $id )
	{

		$article = SupportArticle::wherePermalink( $id )->whereSiteId( $this->site->id )->first();
		if( $article )
			return $this->show( $article );
		\App::abort( '404', 'Article not found' );
	}

}