<?php namespace App\Http\Controllers\Api;

use App\Models\SupportCategory;
use App\Models\SupportArticle;
use App\Http\Controllers\Api\SiteMetaDataController;

class SupportCategoryController extends SMController
{
    public function __construct(){
        parent::__construct();

        $this->middleware("auth",['except' => array('index','show')]);
        $this->middleware('admin',['except'=>array('index','show')]); 

        $this->model = new SupportCategory();       
    }

    public function store()
    {
        \Input::merge(array('site_id'=>$this->site->id));
        $category = parent::store();
        $category->articles = array();
        return $category;
    }

    public function update($model)
    {
        \Input::merge(array('site_id'=>$this->site->id));
        $category = parent::update($model);
        return $category;
    }
	
	public function index(){
		if( \Input::has('view') && \Input::get('view') == 'admin' )
		{
			return parent::paginateIndex();
		}
		else
		{
			$page_size = config( "vars.default_page_size" );

			if( !$this->site )
			{
				$error = array( "message" => 'This site does not exist. Please check URL.', "code" => 500 );
				return response()->json( $error )->setStatusCode( 500 );
			}

			$site_id = $this->site->id;

			$categories = $this->model->where( function ( $query ) use ( $site_id )
			{
				//$company    = Company::getCurrentSiteCompany( $this->site->id );
				//$company_id = $company->id;
				$query->where( 'site_id', $site_id );
					/*->orwhere( function ( $q ) use ( $company_id )
					{
						$q->whereCompanyId( $company_id )->whereSiteId( 0 );
					} );*/
			} );

			$categories = empty( \Input::get( 'public_view' ) ) || \Input::get( 'public_view' ) != true ? $categories->take( $page_size ) : $categories;

			$categories = $categories->orderBy( 'sort_order', 'ASC' );

			foreach( \Input::all() as $key => $value )
			{
				switch( $key )
				{
					case 'q':
						$categories = $this->model->applySearchQuery( $categories, $value );
						break;
					case 'p':
						$categories->skip( ( \Input::get( 'p' ) - 1 ) * $page_size );
						break;
					default:
				}
			}

			$categories = $categories->get();

			$page_meta  = new SiteMetaDataController;
			$sort_order = $page_meta->getItem( 'default_category_sort_order' );

			if( ( empty( \Input::get( 'category_list' ) ) || \Input::get( 'category_list' ) != true ) )
			{
				foreach( $categories as $category )
				{
					$category->articles = SupportArticle::whereCategoryId( $category->id )
						->where( function ( $query ) use ( $site_id )
						{
							//$company    = Company::getCurrentSiteCompany( $this->site->id );
							//$company_id = $company->id;
							$query->where( 'site_id', $site_id );
								/*->orwhere( function ( $q ) use ( $company_id )
								{
									$q->whereCompanyId( $company_id )->whereSiteId( 0 );
								} ); */
						} )->get();
				}

				$unassigned_articles = SupportArticle::where( function ( $query ) use ( $site_id )
				{
					//$company = Company::getCurrentSiteCompany( $this->site->id );
					$query->where( function ( $query ) use ( $site_id )
					{
						//$company    = Company::getCurrentSiteCompany( $this->site->id );
						//$company_id = $company->id;
						$query->where( 'site_id', $site_id );
							/*->orwhere( function ( $q ) use ( $company_id )
							{
								$q->whereCompanyId( $company_id )->whereSiteId( 0 );
							} );*/
					} );
				} )->whereCategoryId( 0 )->get();
				$default_category    = array( 'id' => 0, 'site_id' => $this->site->id, 'sort_order' => $sort_order + 1, 'title' => '', 'articles' => $unassigned_articles );
				$categories->add( $default_category );
			}
			$categories = $categories->all();
			return ( $categories );
			usort( $categories, function ( $a, $b )
			{
				return $a[ 'sort_order' ] > $b[ 'sort_order' ];
			} );
			return $categories;
		}
	}    

	public function destroy($model){        
        foreach ($model->articles as $article) {
            $article->category_id = 0;
            $article->save();
        }
        return parent::destroy($model);
    }

	public function creator(){
		$categories = \Input::get();
		foreach ($categories as $cat_sort => $category_data) {
			
			$category = SupportCategory::find($category_data["category_id"]);
			if(!$category)
			{
				//continue;
				$articles = $category_data["articles"];
				foreach ($articles as $art_sort => $article_data) {
					$article = SupportArticle::find($article_data["article_id"]);
					if(!$article)
					{
						
						continue;
					}
					$article->category_id=$article_data["category_id"];
					$article->sort_order = $art_sort + 1;
					$article->save();
				}

			    $page_meta = new \App\Http\Controllers\Api\SiteMetaDataController;

			    $page_meta->saveItem( 'default_category_sort_order', $cat_sort );
			}
			else
			{
				$category->sort_order = $cat_sort + 1;
				$articles = $category_data["articles"];

				foreach ($articles as $art_sort => $article_data) {
					$article = SupportArticle::find($article_data["article_id"]);
					if(!$article)
						continue;
					$article->sort_order = $art_sort + 1;
					$category->articles()->save($article);
				}

				$category->save();
			}
		}

		return $categories;
	}

}