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
			if( \Input::has('permalink') && !empty( \Input::get('permalink') ) )
			{
				$category = Category::whereSiteId( $this->site->id )->wherePermalink( \Input::get('permalink') )->first();

				$this->model = Post::whereHas('categories', function( $q ) use ( $category ) {
					$q->where( 'categories.id', $category->id );
				});

				\Input::merge( [ 'permalink' => null ] );
			}

			$posts = parent::paginateIndex();

            foreach ($posts['items'] as $i => $post) {
                if($post->access_level_type==4){
                    if (!\App\Helpers\SMAuthenticate::set() || !\SMRole::hasAccess($this->site->id,'view_private_content')){
                        unset($posts['items'][ $i ]);
                    }
                }

            }

            $posts['items'] = array_values($posts['items']->toArray());

			if( !empty( $category ) )
				$posts['category'] = $category;

            return $posts;
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

        $model = $this->model->with("seo_settings",'discussion_settings',"dripfeed","tags","categories","access_level")->whereId($model->id)->first();
       
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

	public static function AllowedColumns()
	{
		return [
			'site_id',
			'author_id',
			'company_id',
			'title',
			'content',
			'note',
			'embed_content',
			'featured_image',
			'access_level_type',
			'access_level_id',
			'permalink',
			'discussion_settings_id',
			'deleted_at',
			'created_at',
			'updated_at',
			'end_published_date',
			'published_date',
			'preview_schedule',
			'transcript_content_public',
			'transcript_content',
			'transcript_button_text',
			'audio_file',
			'always_show_featured_image',
			'show_content_publicly'
		];
	}

	public static function SetAllowedInput()
	{
		$input_fields = [];

		foreach( self::AllowedColumns() as $key => $val )
		{
			if( \Input::has( $val ) || \Input::get($val) == '')
				$input_fields[ $val ] = \Input::get( $val );
		}

		return $input_fields;
	}

    public function store()
    {
       	$stored = $this->model->create( $this->SetAllowedInput() );

		if( !$stored->id )
		{
			App::abort(401, "The operation requested couldn't be completed");
		}

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
		\App\Models\Event::Log( 'updated-post', array(
			'site_id' => $this->site->id,
			'user_id' => \Auth::user()->id,
			'post-title' => $model->title,
			'post-id' => $model->id
		) );
		$model->fill( $this->SetAllowedInput() );
		$model->save();

		return $model;
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