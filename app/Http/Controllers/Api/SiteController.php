<?php namespace App\Http\Controllers\Api;

use App\Models\ShortCode;
use App\Models\SupportArticle;
use App\Models\TeamRole;
use App\Models\Site\Role;
use App\Models\Site;
use App\Models\AppConfiguration;
use App\Models\SiteAds;
use App\Models\AccessLevel;
use App\Models\AccessLevel\Pass;
use App\Models\SupportTicket;
use App\Models\EmailSetting;
use App\Models\ContentStats;
use App\Models\Lesson;
use App\Models\Directory;

use App\Models\Download;
use App\Models\Livecast;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Wizard;
use App\Models\MemberMeta;
use Auth;
use Input;
use PRedis;
use App\Jobs\CloneSite;

class SiteController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware("auth", ['except' => array('getBestSellingSites','getBySubdomain','getAllSites', 'details', 'getLatestOfAllContent','getTicketCount','SMUrl' , 'directory' , 'search')]);
        $this->middleware("smember", ['only' => array('store')]);
        $this->model = new Site();

    }

    public function index()
    {
		if( !\Input::has('cloneable') || \Input::get('cloneable') != 1 )
		{
			return parent::index();
		}
		elseif( \Input::has('cloneable') && \Input::get('cloneable') == 1 )
		{
            $dfy_sites = $this->model->where('cloneable', 1)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
			return
                array(
                'sites' => \Auth::user()->sitesWithCapability('clone_site'),
                'dfy_sites' => $dfy_sites
                );
		}
		else
		{
			$query = $this->model;

			$query = $query->orderBy('id' , 'DESC');
			$query = $query->whereNull('deleted_at');
			foreach (Input::all() as $key => $value){
				switch($key){
					case 'q':
						$query = $this->model->applySearchQuery($query,$value);
						break;
					case 'bypass_paging':
						break;
					default:
						$query->where($key,'=',$value);
				}
			}

			return $query->get();
		}
    }

    public function store(){
        if(!preg_match('#^[a-z0-9]{1}(?:[a-z0-9\-]{0,61}[a-z0-9]{1})?$#i', Input::get('subdomain'))){
          \App::abort(403,"This subdomain is not in a valid format. Only Alphanumeric and '-' is allowed");
        }

        $user = Auth::user();
        if (!$user)
        {
            \App::abort(403, "Failed to create site, please contact support");
        }

        $subdomain = Input::get('subdomain');

        if(isset($subdomain) && $subdomain && !empty($subdomain)){
            $exists = Site::whereSubdomain($subdomain)->first();

            if($exists && isset($exists->id)){
                \App::abort(403, "This subdomain already exists. Please choose a different one");
            }
        }
        
        $site = parent::store();

		$clone_id = \Input::get('clone_id');

		if( empty( $clone_id ) )
				$clone_id = \Config::get('vars.default_site_to_clone');
		
        if( !empty( $clone_id ) ){
            $site_clone = Site::find($clone_id);
            $site->total_lessons = $site_clone->total_lessons;
			$site->syllabus_format = $site_clone->syllabus_format;
			$site->show_syllabus_toggle = $site_clone->show_syllabus_toggle;
			$site->welcome_content = $site_clone->welcome_content;
            $site->save();
            \Log::info('cloning sites');
            $this->model->clone_site($site->id , $clone_id , \Auth::user()->id );
        }

		$total_created = MemberMeta::get( 'sites_created', \Auth::user()->id );

		if( $total_created )
			$site->sites_created = $total_created->value;
		else
			$site->sites_created = 0;
		
        return $site;
    }

    public function destroy($model){
        $model->subdomain = $model->id;
        $model->name = $model->id;
        $model->save();
        return parent::destroy($model);
    }

    public function search(){
        $value = \Input::get('q');
        $page = \Input::get('p');
        $sort_by = \Input::get('sort_by');
        $is_paid = \Input::get('is_paid');
        $rating = \Input::get('rating');
        if(empty($sort_by))
        {
            $column='total_members';
            $order = 'desc';
        }else{
            $column = explode(',', $sort_by)[0];
            $order = explode(',', $sort_by)[1];
        }

        if(empty($page)){
            $page = 1;
        }
        $count = 0;
        $query = Directory::whereNull('deleted_at')->where('is_visible' , true);
        
        $query = $query->where(function ($query) use($value) {
            $query->where('title', 'like','%' . $value . "%")->orWhere('description', 'like','%' . $value . "%");
        });
        if(!empty($is_paid)){
            $is_paid = $is_paid === 'true'? true: false;
            $query = $query->where('is_paid' , '=', $is_paid);
        }
        if(!empty($rating)){
            $query = $query->whereIn('rating' , $rating);
        }
        $results['total_count'] = $query->count();
        $query = $query->with('site','site.owner','site.reviews')->orderBy($column , $order)->limit(25)->offset(($page - 1) * 25);
        //dd($query->toSql());
        $results['items'] = $query->get();
        return $results;
    }

	public function update($model)
	{
		$model = parent::update( $model );

		return $model;
	}

    public function addMember()
    {
        $user = Auth::user();

        $site_id = \Input::get('site_id');

        if($site_id){
            $site = Site::find($site_id);
            if(isset($site)){
                $site->addMember($user , 'member');
            }
            return $site;
        }

        $this->site->addMember($user, 'member');
        return $user;
    }

    public static function getOrSetFacebookId($data , $user_id, $site_id){

        $redis_data = PRedis::get( 'facebook_group_id:' . $site_id .':'.$user_id);

        if( $redis_data )
            return $redis_data;

        if (\Auth::check()){
          if($data->is_admin){
            $accessLevel = AccessLevel::whereSiteId($site_id)->where('facebook_group_id' , '!=' , '')->first();
            if($accessLevel)
              $data->facebook_group_id = $accessLevel->facebook_group_id;
          }
          else{
            $passes = Pass::whereSiteId($site_id)->whereUserId(\Auth::user()->id)->get();
            foreach ($passes as $pass) {
              if($pass){
                $accessLevel = AccessLevel::find($pass->access_level_id);
                if($accessLevel && $accessLevel->facebook_group_id){
                  $data->facebook_group_id = $accessLevel->facebook_group_id;
                  break;
                } else {
                    $grants = $accessLevel->grants;
                    if ($grants)
                    {
                        foreach ($grants as $grant)
                        {
                            $grant_access_level = AccessLevel::find($grant->grant_id);
                            if($grant_access_level && $grant_access_level->facebook_group_id) {
                                $data->facebook_group_id = $grant_access_level->facebook_group_id;
                                break 2;
                            }
                        }
                    }
                }
              }
            }
          }
        }

        PRedis::setex('facebook_group_id:' . $site_id.':'.$user_id, 24 * 60 * 60, isset($data->facebook_group_id) ? $data->facebook_group_id : '');
        return isset($data->facebook_group_id) ? $data->facebook_group_id : '' ;
    }

    public function details()
    {
        if(!$this->site && \Domain::getSubdomain()!='my' && \Domain::getSubdomain()!='app')
          \App::abort(406,'No such subdomain exists');
        else if( \Domain::getSubdomain()=='my' || \Domain::getSubdomain() == 'app' )
        {
            return [];
        }

		$site_id    = $this->site->id;

		$data = Site::with(
			"menu_items", "footer_menu_items", "meta_data", "ad", "widgets", "widgets.meta_data", "widgets.banner"
		)->whereId( $site_id )->first();
        foreach ($data->widgets as $widget)
        {
            foreach ($widget->meta_data as $single_meta_data)
            {
                if ($single_meta_data->key == 'content')
                {
                    $single_meta_data->value = ShortCode::replaceShortcode($single_meta_data->value);
                }
            }
        }

		$data->header_background_color = $this->site->getHeaderBackgroundColor();

		$data->app_configuration = AppConfiguration::with( 'account' )
			->where( function ( $q ) use ( $site_id )
			{
				$q->where( 'site_id', $site_id );
			} )->whereDisabled( 0 )->get();

        if ( \App\Helpers\SMAuthenticate::set() ){

			$data->last_responses        = SupportTicket::with( 'user' )->where( 'parent_id', '!=', 0 )
				->orderBy( 'last_replied_at', 'desc' )->take( 3 )->get();
			$data->unread_support_ticket = SupportTicket::getUnreadSupportTickets( $this->site );

            $wizard_steps = Wizard::whereSiteId($this->site->id)->whereSlug('site_launch_wizard')->first(['completed_nodes']);
            $data->wizard_step = 0;
            if($wizard_steps){
                $count = explode(',', $wizard_steps);
                $data->wizard_step = count($count);
            }

            $data->wizard_completed = Wizard::whereSiteId($this->site->id)->whereSlug('site_launch_wizard')->first(['is_completed']);

           	$data->can_create_sites = !empty($role);
        }

		$data->is_member = \App\Helpers\SMAuthenticate::isMember($this->site->id);

        if(\Auth::check()){
            //$data->facebook_group_id = self::getOrSetFacebookId($data , \Auth::user()->id, $this->site->id );
            $data->capabilities = \SMRole::getUserCapabilities($this->site->id,\Auth::user()->id);
            $data->current_access_levels = \SMRole::getUserAccessLevels($this->site->id, \Auth::user()->id);
            $data->is_customer = \SMRole::isCustomer(\Auth::user()->id);
        }else{
            $data->current_access_levels = [];
            $data->capabilities = [];
        }

        $data->is_admin = $this->is_admin;
        $data->total_lessons = Lesson::whereSiteId($site_id)->where('access_level_type','!=',4)->whereNull('deleted_at')->count();
        return $data;
    }

    public function membersLight() {

    }

    public function getTicketcount (){
        return SupportTicket::getUnreadSupportTickets( $this->site );
    }

    public function getLatestOfAllContent()
    {
        $lessons = Lesson::whereSiteId($this->site->id)
            ->whereNull('deleted_at')
            ->where('featured_image', '!=', '')
            ->where('access_level_type','!=',4)
            ->orderBy('created_at', 'DESC')

            ->take(7)->skip(0)->get();

        $downloads = Download::whereSiteId($this->site->id)
            ->whereNull('deleted_at')
            ->where('featured_image', '!=', '')
            ->where('access_level_type','!=',4)
            ->orderBy('created_at', 'DESC')
            ->take(4)->skip(0)->get();

        $livecasts = Livecast::whereSiteId($this->site->id)
            ->whereNull('deleted_at')
            ->where('featured_image', '!=', '')
            ->where('access_level_type','!=',4)
            ->orderBy('created_at', 'DESC')
            ->take(2)->skip(0)->get();

        $posts = Post::whereSiteId($this->site->id)
            ->whereNull('deleted_at')
            ->where('featured_image', '!=', '')
            ->orderBy('created_at', 'DESC')
            ->take(6)->skip(0)->get();

        $articles = SupportArticle::whereSiteId($this->site->id)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'DESC')
            ->take(5)->skip(0)->get();

        $comments = Comment::with('user')->whereSiteId($this->site->id)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'DESC')
            ->take(5)->skip(0)->get();

        return array('lessons' => $lessons, 'posts' => $posts, 'downloads' => $downloads, 'livecasts' => $livecasts, 'articles' => $articles, 'comments' => $comments);
    }

    public function members()
    {

        if(!\Auth::check())
            return [];
        $user_id = \Auth::user()->id;
     
        if(Input::has('p')){
            $current_page = Input::get('p');
        }else{
            $current_page = 1;
        }

		if( $user_id == 1 )
		{
			$sites_to_hide = [
				'training',
				'bridgepages',
				'app2'
			];
		}

        $sites = Role::getSites($user_id);
        $data = [];
        $count = count($sites);

        for ($i = 0 ; $i < $count ; $i++) {
            $site = $sites[$i];

			if( empty($site['site']) || ( !empty( $sites_to_hide ) && isset($site['site']['subdomain']) &&  in_array( $site['site']['subdomain'], $sites_to_hide) ) )
			{
				unset( $sites[$i] );
			}
			else
			{
				if( isset( $site['site'] ) && isset( $site['type'] ) ){
					$site['site']['role'] = $site['type'];
                }
                $data[] = $site;  
			}
        }
        
        $data['items'] = array_pluck($data , 'site');
        $data['total_count'] = count($data['items']);
        //$data['items'] = array_slice($data['items'], (($current_page - 1)*25),25);

        return array('items'=> $data['items'], 'total_count' => $data['total_count']);
    }
    
    public function getSummary() 
    {
        if (empty($this->site)) return [''];

        $result = [];
        $data = ContentStats::where('site_id', $this->site->id)->select('meta_key', 'meta_value')->get();

        foreach ($data as $pts)
            $result[$pts['meta_key']] = $pts['meta_value'];

        if (array_key_exists('lessons_top', $result))
        {
            $lesson_ids = explode(',', $result['lessons_top']);
            $lesson_views = explode(',', $result['lessons_top_views']);

            $top_lessons = [];
            $i = 0;
            foreach ($lesson_ids as $lid)
            {
                $lesson = \App\Models\Lesson::select('title', 'id')->find($lid);
                if ($lesson)
                {
                    $lesson->views = $lesson_views[$i];
                    $top_lessons[] = $lesson;
                }
                
                $i++;
                
            }

            $result['lessons_top_list'] = $top_lessons;
        }

        if (array_key_exists('top_downloads', $result))
        {
            $dld_ids = explode(',', $result['top_downloads']);
            $dld_views = explode(',', $result['top_downloads_views']);

            $top_dlds = [];
            $i = 0;
            foreach ($dld_ids as $did)
            {
                $download = \App\Models\Download::select('title', 'id')->find($did);
                if ($download)
                {
                    $download->views = $dld_views[$i];   
                    $top_dlds[] = $download;
                }
                $i++;
            }

            $result['downloads_top_list'] = $top_dlds;
            

        }

        return $result;

    }

	public function updateSiteHash()
	{
		$sites = $this->model->whereNull('hash')->orwhere('hash','')->get();

		foreach( $sites as $site )
		{
			$site->hash = md5( microtime() * rand() );
			$site->save();
		}
	}

	public function SMUrl( $domain )
	{
		$site_data = [];

		if( preg_match( '/^(?:(?:http(?:s)?\:)?\/\/)?([a-z0-9]{1}(?:[a-z0-9\-]{0,61}[a-z0-9]{1})?)\.smartmember\.(?:com|dev|in)$/i', $domain, $matches ) )
		{
			if( !empty( $matches ) )
			{
				$subdomain = $matches[1];

				$site = $this->model->whereSubdomain( $subdomain )->first();

				if( $site )
				{
					$site_data = [
						'sm_url' => 'http://' . $site->subdomain . '.smartmember.com',
						'custom_url' => ( $site->domain ? 'http://' . $site->domain : false ),
						'sm_id' => $site->id
					];
				}
			}
		}
		else
		{
			preg_match( '/^(?<=^|\ |\n)(?:(?:(?:http(?:s)?\:)?(?:\/\/))|(?:\/\/))?((?:(?=[a-z0-9\-\.]{1,255}(?=\/|\ |$|\:|\?|\,|\!))(?:(?:(?:[a-z0-9]{1}(?:[a-z0-9\-]{1,62})?\.){1,127})[a-z]{2,}(?:\.[a-z]{2})?)))(?:[a-z0-9\/\-\_\%\?\&\!\$\'\,\(\)\*\.\+\=\;])*?(?=$|\.(?=\ |$)|\:|\n|\ |\?(?=\ |$)|\,|\!)$/i', $domain, $matches );

			if( !empty( $matches ) )
			{
				$domain = $matches[1];

				$site = $this->model->whereDomain( $domain )->first();

				if( $site )
				{
					$site_data = [
						'sm_url' => 'http://' . $site->subdomain . '.smartmember.com',
						'custom_url' => ( $site->domain ? 'http://' . $site->domain : false ),
						'sm_id' => $site->id
					];
				}
			}
		}

		$view = \View::make("support.sm_url", $site_data)->render();

		echo $view;
		exit;
	}

    public function getBySubdomain(){

	    if( isset($_GET['token']) && $_GET['token'] == 'pbLllwVETx8dxqb8nkiBWAEj' )
	    {
		    $subdomain = $_GET['text'];
	    }
	    else
	    {
		    $subdomain = \Input::get('subdomain');
	    }

        $site = Site::where('subdomain' , $subdomain)->with(['owner' ,'meta_data', 'reviews' , 'reviews.user','directory'])->first();
        if(!empty($site)){
            $site->other_sites = Site::with(['owner' ,'meta_data', 'reviews' , 'reviews.user'])->whereUserId($site->user_id)->where('id','!=',$site->id)->orderBy('total_revenue','desc')->get();
        }

	    if( isset($_GET['token']) && $_GET['token'] == 'pbLllwVETx8dxqb8nkiBWAEj' )
	    {
		    echo $site->name.' (#'.$site->id.') is owned by '.$site->owner->first_name.' <'.$site->owner->email.'> #'.$site->owner->id;
		    return;
	    }

        return $site;
    }

    public function getBestSellingSites() {

        $categories = \Input::get('categories');
        $results = [];
        
        if(!empty($categories))
            foreach ($categories as $key => $category) {
                $results[] = Directory::whereNull('deleted_at')->whereNotNull('image')->where('is_visible' , true)->with(['site' , 'site.owner' , 'site.meta_data' , 'site.reviews'])->where('category' , $category)->orderBy('total_revenue','desc')->take(4)->get();
            }

        return $results;
    }

    public function directory(){

        if(\Input::has('featured')){
            $sub_categories = \Input::get('sub_categories');
            $results = [];
            foreach ($sub_categories as $key => $value) {
                $results[$value] = Directory::whereNull('deleted_at')->where('is_visible' , true)->where('sub_category' , $value)->with(['site' , 'site.owner' , 'site.meta_data' => function($query) { $query->where('key', '=', 'logo_url');}])->orderBy('total_revenue','desc')->take(4)->get();
            }

            return $results;
            
        }

        $category = \Input::get('category');
        $subcategory = \Input::get('sub_category');
        $query =  Directory::whereNull('deleted_at')->where('is_visible' , true)->with(['site' , 'site.owner' , 'site.meta_data' => function($query) { $query->where('key', '=', 'logo_url');}]);

        if(!empty($category)){
            $query->where('category' , $category)->orderBy('total_revenue','desc');
        }

        if(!empty($subcategory)){
            $query->where('sub_category' , $subcategory)->orderBy('total_revenue','desc');
        }          

        $page = \Input::get('p');
        if(empty($page)){
            $page = 1;
        }
        $count = 0;
        $results['total_count'] = $query->count();
        $query = $query->orderBy('total_revenue' , 'desc')->limit(25)->offset(($page - 1) * 25);
        $results['items'] = $query->get();
        
        return $results;
    }
}
