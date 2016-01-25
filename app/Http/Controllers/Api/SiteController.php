<?php namespace App\Http\Controllers\Api;

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
        $this->middleware("auth", ['except' => array('details', 'getLatestOfAllContent')]);
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
			return \Auth::user()->sitesWithCapability('clone_site');
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
        if(preg_match('/[^a-z_\-0-9]/i', Input::get('subdomain'))){
          \App::abort(403,"This subdomain is not in a valid format. Only Alphanumeric is allowed");
        }

        $user = Auth::user();
        if (!$user)
        {
            \App::abort(403, "Failed to create site, please contact support");
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

        if(!$this->site && \Domain::getSubdomain()!='my')
          \App::abort(406,'No such subdomain exists');
        else if(\Domain::getSubdomain()=='my')
        {
            return [];
        }

		$site_id    = $this->site->id;

		$data = Site::with(
			"menu_items", "footer_menu_items", "meta_data", "ad", "widgets", "widgets.meta_data", "widgets.banner"
		)->whereId( $site_id )->first();

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

        return $data;
    }

    public function membersLight() {

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
        /*$companies = Company::getUsersCompanies();
        $admin_sites = !empty( $companies['sites'] ) ? $companies['sites'] : [];
        
        $current_company = Auth::user()->options('current_company_id')->first();

        if (!isset($current_company))
        {
            if (count($companies['companies']) > 0) {
                $current_company = $companies['companies'][0];
                $current_company_id = $current_company->id;
            }

            if (isset($current_company) && isset($admin_sites[$current_company->id]))
              $sites = $admin_sites[$current_company->id];
            else
              $sites = array();
        }
        else
        {
            if (isset($admin_sites[$current_company->meta_value]))
            {
                $sites = $admin_sites[$current_company->meta_value];

                $current_company_id = $current_company->meta_value;
            }
            else {
                $sites = array();
            }
        } 

        $page_size = config("vars.default_page_size");
        $user = \Auth::user();
        $user_id = $user->id;

        if( !empty( $current_company_id ) )
            $company_role = TeamRole::whereCompanyId( $current_company_id )->whereUserId( $user_id )->where('role','<',6)->first();

        if( !empty( $current_company_id ) && isset( $company_role ) && $company_role && count( $company_role ) > 0 )
            $site_ids = Site::whereCompanyId( $current_company_id )->select('id')->lists('id');

        $admin_roles = Role::with('type')
                               ->whereHas('type', function($query){
                                    $query->where('role_type','<',5);
                               })
                               ->whereUserId(Auth::user()->id)
                               ->whereIn('site_id', $sites)
                              ->get();

        $member_roles = Role::with('type')
                       ->whereHas('type', function($query){
                           $query->where('role_type', 6);
                       })
                       ->whereUserId(Auth::user()->id)
                       ->where('site_id', '!=', 0)
                       ->where('site_id', '!=', 6192);

        if( !empty( $site_ids ) )
            $member_roles = $member_roles->whereNotIn('site_id',$site_ids );

        $member_roles = $member_roles->get();

        $ids = $admin_roles->lists('site_id');

        if( !empty( $site_ids ) )
        {
            foreach( $site_ids as $single_site_id )
                $ids->add( $single_site_id );
        }

        $admin = Site::with('meta_data')
                      ->whereIn('id', $ids)
                      ->orderBy('total_revenue', 'DESC');

		if( \Input::has('q') && !empty( \Input::get('q') ) )
			$admin = Site::applySearchQuery( $admin, \Input::get('q') );

        $adminCount=$admin->count();
        $admin = $admin->take($page_size);
        $admin->skip((\Input::get('p')-1)*$page_size);

        $admin = $admin->get();

        $ids = $member_roles->lists('site_id');

        $members = Site::with('meta_data')
                       ->whereIn('id', $ids)
                       ->orderBy('total_revenue', 'DESC');
        $memberCount=$members->count();
        $members = $members->take($page_size);
        $members->skip((\Input::get('p')-1)*$page_size);
        $members = $members->get();


        foreach ($members as $member)
        {
            $member['access'] = Pass::with('accessLevel')->where('user_id', Auth::user()->id)->where('site_id', $member->id)->get();
        }
        */

        if(!\Auth::check())
            return [];
        $user_id = \Auth::user()->id;
        /*$admin = Role::getAdminSites($user_id);
        $members = Role::getMemberSites($user_id);

        $admin = $admin->lists('site');
        $members = $members->lists('site');

        $adminCount = count($admin);
        $memberCount = count($members);
        return ['admin'=>$admin, 'member' => $members];*/

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
        $data = array_pluck($data , 'site');

        return $data;
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
		$sites = $this->model->all();

		foreach( $sites as $site )
		{
			$site->hash = md5( microtime() * rand() );
			$site->save();
		}
	}
}
