<?php namespace App\Http\Controllers\Api;

use App\Helpers\SMAuthenticate;
use App\Http\Controllers\ApiController;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Permalink;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\Site;
use App\Models\User;


class PostController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        //TODO: check if lesson is free
        $this->middleware("auth", ['except' => array('getByPermalink','index','show', 'getBlogPosts', 'getByPermalinkForBlog')]);
        $this->middleware('access' , ['only'=>array('show' , 'single' ,'getByPermalink', 'getByPermalinkForBlog')]);
        $this->middleware('admin',['except'=>array('index','show' ,  'getByPermalink', 'getBlogPosts', 'getByPermalinkForBlog')]);
        $this->model = new Post();
    }



    public function index(){
        if( \Input::has('view') && \Input::get('view') == 'admin' )
		{
			return parent::paginateIndex();
		}
		else
		{
			$page_size = config( "vars.default_page_size" );
			$query     = $this->model;
			$query     = $query->take( $page_size );
			$query     = $query->whereNull( 'deleted_at' );
			$query     = $query->with( 'users' );
			$query     = $query->orderBy( 'id', 'DESC' );
			foreach( \Input::all() as $key => $value )
			{
				switch( $key )
				{
					case 'q':
						$query = $this->model->applySearchQuery( $query, $value );
						break;
					case 'p':
						$query->skip( ( \Input::get( 'p' ) - 1 ) * $page_size );
						break;
					default:
						$query->where( $key, '=', $value );
				}
			}
			$posts = $query->get();

			foreach( $posts as $i => $post )
			{
				if( !$post->preview_schedule )
				{
					if( !SMAuthenticate::checkScheduleAvailability( $post ) || !SMAuthenticate::checkAccessLevel( $post ) )
					{
						unset( $posts[ $i ] );
					}
				}

				$post->comment_count = Comment::whereType( 'post' )->whereTargetId( $post->id )->count();
			}
            $posts = array_values($posts->toArray());
			return $posts;
		}
    }

    public function show($model)
    {
        if($model->discussion_settings_id == 0){
            $this->model->addDiscussionSettings($model);
        }

        $most_used = $this->getMostUsed($model->site_id);

        $model = $this->model->with("seo_settings",'discussion_settings',"tags","categories","access_level")->whereId($model->id)->first();
       
        $model->most_used_categories = $most_used['most_used_categories'];
        $model->most_used_tags = $most_used['most_used_tags'];
        return $model;
    }

    public function getMostUsed($site_id)
    {
        $most_used_categories = [];
        $most_used_tags = [];

        $most_used_tags_data = \DB::select('select tag_id as id from posts_tags where post_id in (select id from posts where site_id = ?)  group by tag_id order by count(tag_id) desc limit 3', [$site_id]);
        $most_used_categories_data = \DB::select('select category_id as id  from posts_categories where post_id in (select id from posts where site_id = ?)  group by category_id order by count(category_id) desc limit 3', [$site_id]);
        
        for ($i=0; $i < count($most_used_categories_data); $i++) { 
            $most_used_categories[] = Category::whereId( $most_used_categories_data[$i]->id)->first();
        }

        for ($i=0; $i < count($most_used_tags_data); $i++) { 
            $most_used_tags[] = Tag::whereId($most_used_tags_data[$i]->id)->first();
        }
        return ['most_used_tags'=>$most_used_tags , 'most_used_categories'=>$most_used_categories];
    }

    public function store()
    {
       	$stored = parent::store();

		\App\Models\Event::Log( 'created-post', array(
			'site_id' => $this->site->id,
			'user_id' => \Auth::user()->id,
			'post-title' => $stored->title,
			'post-id' => $stored->id
		) );

       	return $stored;
    }

	public function destroy($model)
	{
		$permalinks = Permalink::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->get();
		foreach( $permalinks as $permalink )
			$permalink->delete();

		\App\Models\Event::Log( 'deleted-post', array(
			'site_id' => $this->site->id,
			'user_id' => \Auth::user()->id,
			'post-title' => $model->title,
			'post-id' => $model->id
		) );

		return parent::destroy($model);
	}

    public function update($model)
    {
        $categories = \Input::get('categories');
        $categoriesID=array_values(array_column($categories,'id'));
        $deleteCategories = PostCategory::whereNotIn('category_id',$categoriesID)->get(array('id'));
        \Log::info(array_pluck($deleteCategories,'id'));
        if(sizeof($deleteCategories)>0)
            $model->categories()->detach(array_pluck($deleteCategories,'id'));

        $tags = \Input::get('tags');
        $tagsID=array_values(array_column($tags,'id'));
        $deleteTags = \DB::table('posts_tags')->whereNotIn('tag_id',$tagsID)->get(array('id'));
        \Log::info(array_pluck($deleteTags,'id'));
        if(sizeof($deleteTags)>0)
            $model->tags()->detach(array_pluck($deleteTags,'id'));

		\App\Models\Event::Log( 'updated-post', array(
			'site_id' => $this->site->id,
			'user_id' => \Auth::user()->id,
			'post-title' => $model->title,
			'post-id' => $model->id
		) );

        return $model->update(\Input::except('_method' , 'access'));
    }

    public function getByPermalink($id){
        $post = Post::wherePermalink($id)->whereSiteId($this->site->id)->first();
        if($post)
            return $this->show($post);
        \App::abort('404','POst not found');
    }

    public function getByPermalinkForBlog($id)
    {
        $blog_site = Site::whereSubdomain(['help'])->first();
        $post = Post::wherePermalink($id)->whereSiteId($blog_site->id)->first();
        if($post)
        {
            $post->author = User::find($post->author_id);
            $post->author->email_hash = md5($post->author->email);
            return $post;
        }
        \App::abort('404','Post not found');
    }

    function truncateWord($text, $limit)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }

    function truncateParagraph($text)
    {
        $index_p = strpos($text, "</p>");
        if( !$index_p && $index_p !== 0 )
            return $text;

        $para_1 = substr($text, 0, $index_p + 4);
        $remaining_p = substr($text, $index_p + 4);
        $index_p_2 = strpos($remaining_p, "</p>");
        $para_2 = substr($remaining_p, 0, $index_p_2 + 4);
        return $para_1 . $para_2;
    }

    public function getBlogPosts()
    {
        $page_size = config("vars.default_page_size");
        $blog_site = Site::whereSubdomain(['help'])->first();
        if (\Input::has('p'))
            $page = \Input::get('p');
        else
            $page = 1;
        $posts = Post::where('site_id', '=', $blog_site->id)->orderBy('created_at', 'DESC')->take($page_size)->skip(($page - 1) * $page_size)->get();
        foreach ($posts as $post)
        {
            $post->summary = $this->truncateParagraph(strip_tags($post->content, '<p>'));
            $post->author = User::find($post->author_id);
            $post->author->email_hash = md5($post->author->email);
        }
        return $posts;
    }

}