<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Role;
use App\Models\Site\Role as SiteRole;
use App\Models\AccessLevel;
use App\Models\AccessLevel\Pass;
use App\Models\SiteMetaData;

use App\Helpers\SMAuthenticate;
use Illuminate\Support\Facades\Input;
use Auth;
use PRedis;

class ModuleController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware("auth", ['except' => 'home']);
        $this->middleware('admin',['except'=>array('index','show','home')]); 
        $this->model = new Module();
    }

    public function index()
    {
        Input::merge(array('site_id' => $this->site->id));
        return parent::paginateIndex();
    }

    public function update($model){
        $model->update(Input::except('_method'));
        return $model;
    }

    public function store()
    {   
        $prev_module =  Module::whereSiteId($this->site->id)->orderBy('sort_order' , 'desc')->first();
        if($prev_module)
            Input::merge(array('sort_order'=>$prev_module->sort_order + 1));
        else
            Input::merge(array('sort_order'=>1));

        Input::merge(array('site_id' => $this->site->id));
        return parent::store();
    }

    public function show($model)
    {
        if (\Input::has('view') && \Input::get('view') == 'public')
        {
            return $this->home($model->id);
        } else {
            $model = $model->find($model->id);
            return parent::show($model);
        }

    }

    public function destroy($model){        
        foreach ($model->lessons as $lesson) {
            $lesson->module_id = 0;
            $lesson->save();
        }
        return parent::destroy($model);
    }

    public function home($module_id = false)
    {
		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}
        /*
        if(SMAuthenticate::set()){
            $redis_modules = PRedis::get('modules:'.$this->site->id.':'.\Auth::user()->id);
            if($redis_modules){
                \Log::info('getting modules from redis for logged in user');
                return json_decode($redis_modules);
            }
        }else{
            $redis_modules = PRedis::get('modules:'.$this->site->id.':0');
            if($redis_modules){
                \Log::info('getting modules from redis for non-logged in user');
                return json_decode($redis_modules);
            }
        }
        */
        $is_authenticated = false;
        $is_admin = false;

        //Track visits
        if (SMAuthenticate::set()){
            $is_authenticated = true;
            if (isset($this->site->id))
            {
                $is_admin = \SMRole::hasAccess($this->site->id,'view_private_content');
                Role::incrementVisits($this->site->id);
            }
        }
        if(\Auth::check()){
            $modules = $this->model->with("lessons" , "lessons.userNote", "lessons.access_level")->whereSiteId
            ($this->site->id);
            if ($module_id)
                $modules = $modules->whereId($module_id);
            $modules = $modules->get();
            $unassigned_lessons = Lesson::with('userNote')->whereSiteId($this->site->id)->whereModuleId(0)->orderBy('sort_order','asc')->get();
        }
        else{
            $modules = $this->model->with("lessons")->whereSiteId($this->site->id);
            if ($module_id)
                $modules = $modules->whereId($module_id);
            $modules = $modules->get();
            $unassigned_lessons = Lesson::whereSiteId($this->site->id)->whereModuleId(0)->orderBy('sort_order','asc')->get();
        }

        $page_meta = new \App\Http\Controllers\Api\SiteMetaDataController;

        //\Log::info($unassigned_lessons);//disabled this because it makes the log really hard to read
        $sort_order = $page_meta->getItem( 'default_module_sort_order' );

        $default_module = new Module(array('id'=>0,'site_id'=>$this->site->id,'sort_order'=>$sort_order,'title'=>'','access_level'=>0, 'lessons' => $unassigned_lessons ) );

        $modules->add( $default_module );

        $count = 0;
        $modules_length = count( $modules );
        $modules = $modules->sortBy(function($module, $key){
            global $count, $modules_length;
            if( !isset( $module->sort_order ) || $module->sort_order == 0) {
                $count++;
                return $modules_length + $count;
            } else {
                return $module->sort_order;
            }
        })->values()->all();
        if (SMAuthenticate::set()) {
            $access_passes = SiteRole::whereSiteId($this->site->id)->whereUserId(\Auth::user()
                ->id)->get();
        }

        //Suspicious code
        foreach ($modules as $module) {
            foreach ($module->lessons as $i=>$lesson) {
                if($lesson->access_level_type==4){
                    if (!$is_authenticated || !$is_admin){
                        unset($module->lessons[$i]);
                    }
                }
                if($lesson->access_level_type==2) {
                    if ($lesson->access_level && $lesson->access_level->hide_unowned_content && !$is_admin)
                    {
                        if ($is_authenticated) {
							$unset = true;

                            foreach ($access_passes as $pass) {
                                $access_levels = Pass::access_levels($pass->access_level_id);

                                if ($pass && in_array($lesson->access_level_id, $access_levels))
								{
									$unset = false;
									break;
								}
                            }

							if( $unset )
								unset($module->lessons[$i]);

                        } else {
                            unset($module->lessons[$i]);
                        }
                    }
                }
                /*(if (!$lesson->preview_schedule && $lesson->published_date != '0000-00-00 00:00:00') {
                    \Log::info('Checking schedule ' . $lesson->id);
                    if (!SMAuthenticate::checkScheduleAvailability($lesson)) {
                        unset($module->lessons[$i]);
                    }
                }
                if (!$lesson->preview_dripfeed) {
                    if (!SMAuthenticate::checkDripAvailability($lesson)) {
                        unset($module->lessons[$i]);
                    }
                }*/
                //$lesson->access_level = AccessLevel::find($lesson->access_level_id);
            }

        }
        /*
        if(SMAuthenticate::set()){
            PRedis::setex('modules:'.$this->site->id.':'.\Auth::user()->id, 15 * 60, json_encode($modules));
        }else{
            PRedis::setex('modules:'.$this->site->id.':0', 60 * 60, json_encode($modules));
        }
        */
        return $modules;
    }

    public function syllabus()
    {
		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

        $site_id = $this->site->id;
        $modules = $this->model->
        select(['*', \DB::raw('IF(`sort_order` != 0, `sort_order`, 0) `sort_order`')])->
        with("lessons","lessons.dripfeed")->
        whereSiteId($site_id)->orderBy("sort_order")->get();

        $unassigned_lessons = Lesson::with("dripfeed")->whereSiteId($site_id)->whereModuleId(0)->orderBy('sort_order')->get();

        $page_meta = new \App\Http\Controllers\Api\SiteMetaDataController;

        $sort_order = $page_meta->getItem( 'default_module_sort_order' );
        $default_title = $page_meta->getItem( 'default_module_title' );

        $default_module = array('id'=>0,'site_id'=>$site_id,'sort_order'=>$sort_order,'title'=>'','access_level'=>0, 'lessons' => $unassigned_lessons );

        $modules->add( $default_module );
        
        $count = 0;
        global $modules_length;
        $modules_length = count( $modules );
        $modules = $modules->sortBy(function($module, $key){
            global $count, $modules_length;
            if( !isset( $module->sort_order ) || $module->sort_order == 0) {
                $count++;
                return $modules_length + $count;
            } else {
                return $module->sort_order;
            }
        })->values()->all();

        usort($modules, function($a, $b){
            return $a['sort_order'] > $b['sort_order'];
        });
        return ['modules' => $modules];
    }

    public function syllabusSave()
    {
        //TODO: check if user is owner
        $input = \Input::get();
        $modules_sorting = [];
        $previous = 0;
        $next = 0;

        foreach ($input as $key => $val ) {
            if( !empty( $val["lesson"] ) )
            {
                $lesson = Lesson::find($val["lesson"]);
                $lesson->sort_order = $key + 1;
                $lesson->module_id = $val["module"];
                $lesson->save();
            }
            $modules_sorting[$val["module"]] = $key;
        }

        $inc = 1;
        foreach ($modules_sorting as $key => $value) {
            if( $key === 0 )
            {
                $page_meta = new \App\Http\Controllers\Api\SiteMetaDataController;

                $page_meta->saveItem( 'default_module_sort_order', $inc );
            }
            else
            {
                $module = Module::find($key);
                if ($module) {
                    $module->sort_order = $inc;
                    $module->save();
                }
            }
            $inc++;
        }


        $lessons = Lesson::whereSiteId($this->site->id)->orderBy('sort_order')->get();
        for ($i = 0 ; $i < count($lessons) ; $i++) {
            $lessons[$i]->prev_lesson = $previous;
            $lessons[$i]->next_lesson = $i + 1 < count($lessons) ? $lessons[$i + 1]->id : 0;
            $previous = $lessons[$i]->id;
            $lessons[$i]->save();
        }

        return $lessons;
    }
}
