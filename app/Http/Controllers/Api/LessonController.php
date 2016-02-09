<?php namespace App\Http\Controllers\Api;

use App\Helpers\VimeoHelper;
use App\Helpers\SMAuthenticate;
use App\Http\Controllers\ApiController;
use App\Models\VimeoIntegration;
use App\Models\AppConfiguration\Vimeo;
use App\Models\MemberMeta;

use App\Models\Lesson;
use App\Models\SiteNotice;
use App\Models\Role;
use App\Models\SiteMenuItem;
use App\Models\SiteFooterMenuItem;
use App\Models\SiteMetaData;
use App\Models\AccessLevel\Pass;
use App\Models\AccessLevel;
use App\Models\Post;
use App\Models\Permalink;
use Auth;
use Input;
use Carbon\Carbon;
use SMCache;


/*
    TODO: THis whole controller needs to be refactored. 
*/
class LessonController extends SMController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware("auth",['except' => array('index','show','getByPermalink','fixUrlForSite')]);
        $this->middleware('access' , ['only'=>array('show' , 'single' ,'getByPermalink')]);
        $this->middleware('admin',['except'=>array('index','show' , 'getByPermalink','fixUrlForSite','getLessonByName')]);
        $this->model = new Lesson();
    }

	public function index()
	{
		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

		\Input::merge(array('site_id' => $this->site->id));
		$return = parent::paginateIndex();
		foreach ($return['items'] as $i=>$lesson) {
			if($lesson->access_level_type==4){
				if (!\App\Helpers\SMAuthenticate::set() || !\SMRole::hasAccess($this->site->id,'view_private_content') ){
					unset($return['items'][$i]);
				}
			}
			if($lesson->access_level_type==2){
				if(!SMAuthenticate::checkIfUnowned($lesson)){
					unset($return['items'][$i]);
				}
			}
			$lesson->access_level = AccessLevel::find($lesson->access_level_id);
		}

		return $return;
	}

    public function getLessonByName($id)
    {
        $lesson = Lesson::whereTitle($id)->whereSiteId($this->site->id)->first();
        if($lesson){
            return $this->show($lesson);
        }
        \App::abort('404','Lesson not found');
    }

    public function show($model)
    {
        if($model->discussion_settings_id == 0){
            $this->model->addDiscussionSettings($model);
        }
        
        if (SMAuthenticate::set()){
            Role::incrementLessons($this->site->id);
        }
        $model = $model->with("access_level", "seo_settings","discussion_settings","module","dripfeed")->find($model->id);

        $model = $this->model->getStatistics($model);
        return parent::show($model);
    }

    public function store()
    {
		if( \SMRole::userHasAccess( $this->site->id, 'manage_content', \Auth::user()->id ) )
		{
			\Input::merge(['site_id' => $this->site->id ]);
			$prev_lesson = Lesson::whereSiteId( $this->site->id )->where( 'module_id', '!=', '0' )
				->orderBy( 'sort_order', 'desc' )->first();
			if( $prev_lesson )
				Input::merge( array( 'sort_order' => $prev_lesson->sort_order + 1 ) );
			else
				Input::merge( array( 'sort_order' => 1 ) );

			$stored      = parent::store();
			$courseTitle = SiteMetaData::whereSiteId( $this->site->id )->whereKey( 'course_title' )->first();

			if( $courseTitle != null )
				$notification = array_merge( array( 'title' => 'lesson alert' ), array( 'content' => $stored->title ), array( 'start_date' => Carbon::now() ), array( 'end_date' => Carbon::now()
					->addDay() ), array( 'site_id' => $this->site->id ), array( 'type' => 'lesson' ) );
			else
				$notification = array_merge( array( 'title' => 'lesson alert' ), array( 'start_date' => Carbon::now() ), array( 'end_date' => Carbon::now()
					->addDay() ), array( 'content' => $stored->title ), array( 'site_id' => $this->site->id ), array( 'type' => 'lesson' ) );
			if( $stored->access_level_type != 4 )
				SiteNotice::create( $notification );

			\App\Models\Event::Log( 'created-lesson', array(
				'site_id' => $this->site->id,
				'user_id' => \Auth::user()->id,
				'lesson-title' => $stored->title,
				'lesson-id' => $stored->id
			) );

			$total_created = MemberMeta::get( 'lessons_created', \Auth::user()->id );

			if( $total_created )
				$stored->lessons_created = $total_created->value;
			else
				$stored->lessons_created = 0;

			return $stored;
		}
		else
		{
			return [];
		}
    }

    public function update($model)
    {   //-- lesson/hahaha
        $newpermalink=\Input::get("permalink");
        if(!\Input::get('site_id'))
            \Input::merge(array('site_id' => $this->site->id ));

		if( \SMRole::userHasAccess( $this->site->id, 'manage_content', \Auth::user()->id ) )
		{
			//homepageURLupdate
			SiteMetaData::where( "key", "homepage_url" )->where( "site_id", $this->site->id )
				->where( "value", "lesson/" . $model->permalink )
				->update( array( "value" => "lesson/" . \Input::get( "permalink" ) ) );
			SiteFooterMenuItem::where( "url", "lesson/" . $model->permalink )->where( "site_id", $this->site->id )
				->update( array( "url" => "lesson/" . \Input::get( "permalink" ) ) );
			SiteMenuItem::where( "url", "lesson/" . $model->permalink )->where( "site_id", $this->site->id )
				->update( array( "url" => "lesson/" . \Input::get( "permalink" ) ) );

			\App\Models\Event::Log( 'updated-lesson', array(
				'site_id' => $this->site->id,
				'user_id' => \Auth::user()->id,
				'lesson-title' => $model->title,
				'lesson-id' => $model->id
			) );

			return $model->update( \Input::except( '_method', 'access' ) );
		}
		else
		{
			return $model;
		}
    }

    public function destroy($model)
	{
		if( \SMRole::userHasAccess( $this->site->id, 'manage_content', \Auth::user()->id ) )
		{
			$notification = SiteNotice::whereContent( $model->title )->first();
			if( $notification != null )
				$notification->delete();
			else
				\Log::info( 'koi msla he vai :' . $model->title );
			$model->site->total_lessons = $model->site->total_lessons - 1;
			$model->site->save();

			$permalinks = Permalink::whereSiteId( $model->site_id )->whereTargetId( $model->id )
				->whereType( $model->getTable() )->get();
			foreach( $permalinks as $permalink )
				$permalink->delete();

			\App\Models\Event::Log( 'deleted-lesson', array(
				'site_id' => $this->site->id,
				'user_id' => \Auth::user()->id,
				'lesson-title' => $model->title,
				'lesson-id' => $model->id
			) );

			return parent::destroy( $model );
		}
		else
		{
			return [];
		}
    }

    public function addAllVideos(){
        $videos = Input::all();

        foreach ($videos as $video)
		{
			$lesson = Vimeo::mapToLesson($video);
            $lesson["site_id"] = $this->site->id;
            $lesson["author_id"] = Auth::user()->id;
            $lesson["access_level_type"] = 4;
            $lesson["access_level_id"] = 0;

			$this->model->create( $lesson );
        }
        return array('success'=>true);
    }

    public function fixUrlForSite($site_id)
    {
        $lessons = Post::where('permalink', 'LIKE', '%2015%')->get();
        foreach ($lessons as $lesson) {
            $lesson->permalink = Lesson::url_slugify($lesson->title);
            echo Lesson::url_slugify($lesson->title) . '<br/>';
            $lesson->save();
        }
    }

    public function getDraftedLesson()
    {
		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

        return Lesson::whereSiteId($this->site->id)->where("is_draft",true)->first();
    }

    public function getByPermalink($id){

		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

        $lesson = Lesson::wherePermalink($id)->whereSiteId($this->site->id)->first();

        if( empty( $lesson ) )
            $lesson = Lesson::whereId($id)->whereSiteId( $this->site->id )->first();

        if(isset($lesson)){
            return $this->show($lesson);
        }

        \App::abort('408','That lesson could not be found');
    }

    public function bulkUpdate(){
        $module_id = \Input::get('module_id');
        //return $module_id;
        $access_level_type = \Input::get('access_level_type');
        $access_level_id = \Input::get('access_level_id') ? \Input::get('access_level_id') : 0 ;
        $lesson_ids = \Input::get('lessons');
        if(!isset($module_id) || !isset($access_level_type))
            return array('success'=>false , 'message'=>'Some required fields missing');
        \DB::table('lessons')
            ->where('module_id','=' ,$module_id)
            ->whereIn('id' , $lesson_ids)
            ->update([ 'access_level_type'=> $access_level_type , 'access_level_id'=> $access_level_id]);
        return array('access_level_type'=> $access_level_type , 'access_level_id'=> $access_level_id);
    }

	public function bulkUpdateAccess()
	{
		$lesson_ids = \Input::get('lesson_ids');
		$access_level_type = \Input::get('access_level_type');
		$access_level_id = \Input::get('access_level_id') ? \Input::get('access_level_id') : 0 ;

		if( !empty( $lesson_ids ) && is_array( $lesson_ids ) )
		{
			\DB::table('lessons')
				->whereSiteId( $this->site->id )
				->whereIn( 'id' , $lesson_ids )
				->update([ 'access_level_type'=> $access_level_type , 'access_level_id'=> $access_level_id]);

			$lesson_key ='modules' . ':' . $this->site->id . ':*';

			$keys = [];
			$keys[] = $lesson_key;

			SMCache::clear($keys);

			$routes[] = 'module_home';
			SMCache::reset($routes);

			return array('access_level_type'=> $access_level_type , 'access_level_id'=> $access_level_id);
		}
	}

	public function bulkDelete()
	{
		$lesson_ids = \Input::get('lesson_ids');
		$module_ids = \Input::get('module_ids');

		if( !empty( $lesson_ids ) && is_array( $lesson_ids ) )
		{
			\DB::table('lessons')
				->whereSiteId( $this->site->id )
				->whereIn( 'id' , $lesson_ids )
				->update([ 'deleted_at' => Carbon::now() ]);

			\DB::table('permalinks')
				->whereSiteId( $this->site->id )
				->whereType('lessons')
				->whereIn( 'target_id' , $lesson_ids )
				->whereNull( 'deleted_at' )
				->update([ 'deleted_at' => Carbon::now() ]);
		}

		if( !empty( $module_ids ) && is_array( $module_ids ) )
		{
			\DB::table('modules')
				->whereSiteId( $this->site->id )
				->whereIn( 'id' , $module_ids )
				->update([ 'deleted_at' => Carbon::now() ]);

			\DB::table('lessons')
				->whereSiteId( $this->site->id )
				->whereIn( 'module_id' , $module_ids )
				->update([ 'module_id' => 0 ]);
		}
		$this->site->total_lessons = $this->site->total_lessons - count($lesson_ids);
		$this->site->save();
		$lesson_key ='modules' . ':' . $this->site->id . ':*';

		$keys = [];
		$keys[] = $lesson_key;

		SMCache::clear($keys);

		$routes[] = 'module_home';
		SMCache::reset($routes);

		return array( 'deleted_modules' => $module_ids , 'deleted_lessons' => $lesson_ids );
	}

    public function single($id){

        return $this->model->with("seo_settings")->whereId($id)->first();
    }

}