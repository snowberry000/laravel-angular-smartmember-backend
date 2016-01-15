<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Directory;
use App\Models\Lesson;
use App\Models\Download;
use App\Models\Transaction;
use App\Models\Site;
use App\Models\Site\Role;
use Auth;
use Input;
use PRedis;

class DirectoryController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware("auth",['except' => array('index','show','approve','byPermalink','categories')]);
        $this->model = new Directory();
    }

    public function index()
    {
        $page = \Input::get('p');

        /*
        $directories = PRedis::get('directories:'.$page);
        if($directories){
            \Log::info('getting from PRedis');
            return $directories;
        }
        */
        $this->model = $this->model
                            ->with(array('site' => function($q) {
                                $q->select('id', 'user_id', 'subdomain', 'domain', 'total_members', 'total_lessons', 'total_revenue');
                            }, 'site.user' => function($q) {
                                $q->select('id','first_name', 'last_name','profile_image','email');
                            }))->where('is_approved', '1');
        $directories = parent::index();
        foreach ($directories as $directory)
        {
            if (empty($directory->site->user->profile_image))
            {
                $directory->site->user->profile_image = "http://www.gravatar.com/avatar" . md5($directory->site->user->email) .  "?d=identicon&f=y";
            }
            unset($directory->site->user->email);
        }

        //PRedis::setex('directories:'.$page, 24 * 60 * 60, $directories);
        return $directories;
    }

    public function categories(){
        return Directory::getAllCategories();
    }
    	

    public function siteListing(){
    	$directory = Directory::whereSiteId($this->site->id)->first();
        if (!$directory)
        {
            $directory = new Directory();

            $directory->hide_lessons = 1;
            $directory->hide_downloads = 1;
            $directory->hide_members = 1;
            $directory->hide_revenue = 1;

            $lesson = new Lesson();
            $download = new Download();
            $transaction = new Transaction();

            $directory->total_members = $this->site->total_members;
            $directory->total_lessons = $lesson->getOne("select count(id) as total_lesson FROM lessons WHERE site_id = " . $this->site->id . " and access_level_type = 4 and deleted_at = NULL", 'total_lesson');
            $directory->total_downloads = $download->getOne("select count(id) as total_downloads FROM download_center WHERE site_id = " . $this->site->id . " and access_level_type = 4 and deleted_at = NULL", 'total_downloads');
            $directory->total_revenue = $transaction->getOne("select SUM(price) as total_sales from transactions where site_id = " . $this->site->id . " and type != 'rfnd'" , 'total_sales');
        }

        $directory->hide_lessons = intval($directory->hide_lessons);
        $directory->hide_members = intval($directory->hide_members);
        $directory->hide_downloads = intval($directory->hide_downloads);
        $directory->hide_revenue = intval($directory->hide_revenue);


        return $directory;

    }

    public function store(){
    
    	if (Input::has('id')){
    		$listing = Directory::find(Input::get('id'));
    		Input::merge(array('pending_updates'=>true));
    		return $this->update($listing);
    	}

    	Input::merge(array('site_id' => $this->site->id));
        $record = $this->model->create(Input::except(['access_token', 'token' , 'send_email',  'total_members']));

        if (!$record->id){
            App::abort(401, "The operation requested couldn't be completed");
        }
        return $record;
    }

    public function update($model){
        Input::merge(['pending_updates'=>true]);
        $model->fill(Input::except('_method' , 'send_email','approve'));
        $model->save();

        if (Input::has('approve')){
            $this->approve($model->site_id);
        }

        return $model;

    }

    public function approve($siteId){

    	//TODO: Allow only super admin to approve
        $keys = PRedis::keys('directories:*');
        foreach ($keys as $key)
        {
            PRedis::del($key);
        }
    	$listing = Directory::whereSiteId($siteId)->first();
		if( $listing )
    		return ['success'=> $listing->approve()];
		else
			return ['success'=> false];
    }

    public function byPermalink($permalink){
        $query = Directory::with(array('site' => function($q) {
                                $q->select('id', 'user_id', 'subdomain', 'domain', 'total_members','total_lessons','total_revenue' );
                            }, 'site.user' => function($q) {
                                $q->select('id','first_name', 'last_name','profile_image','email');
                            }, 'site.meta_data'
                            ));
        $query = $query->where('is_approved', '1')->wherePermalink($permalink)->first();

		if( !$query->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}
        if (empty($query->site->user->profile_image))
        {
            $query->site->user->profile_image = "http://www.gravatar.com/avatar" . md5($query->site->user->email) .  "?d=identicon&f=y";
        }
        unset($query->site->user->email);
        $query->total_members = $query->site->total_members;


        $sites = Role::whereUserId($query->site->user->id)->whereIn('type',['owner'])->get([\DB::raw('distinct(site_id) as id')]);

        foreach ($sites as $key => $site)
        {
            if ($site->id == $query->site->id)
            {
                unset($sites[$key]);
                break;
            }
        }
        $query->sites = Directory::with(array('site' => function($q) {
            $q->select('id', 'user_id', 'subdomain', 'domain', 'total_members','total_lessons','total_revenue');
        }, 'site.user' => function($q) {
            $q->select('id','first_name', 'last_name', 'profile_image','email');
        }))->where('is_approved', '1')
            ->whereIn('site_id' , $sites)
            ->get();

        foreach ($query->sites as $single_site)
        {
            if (empty($single_site->site->user->profile_image))
            {
                $single_site->site->user->profile_image = "http://www.gravatar.com/avatar" . md5($single_site->site->user->email) .  "?d=identicon&f=y";
            }
            unset($single_site->site->user->email);

        }

        $query->hide_lessons = intval($query->hide_lessons);
        $query->hide_members = intval($query->hide_members);
        $query->hide_downloads = intval($query->hide_downloads);
        $query->hide_revenue = intval($query->hide_revenue);
        return $query;


    }



}