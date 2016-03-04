<?php namespace App\Models;

use App\Models\UserNote;
use PRedis;
use App\Http\Controllers\Api\SiteController;
use SMCache;
use App\Models\ShortCode;

class Lesson extends Root
{
    protected $table = 'lessons';

    public function site(){
        return $this->belongsTo('App\Models\Site', 'site_id');
    }
    public function module(){
        return $this->belongsTo('App\Models\Module');
    }
    public function userNote(){
        return $this->hasOne('App\Models\UserNote')->whereUserId(\Auth::user()->id);
    }   
    public function access_level(){
        return $this->belongsTo('App\Models\AccessLevel', 'access_level_id');
    }

    public function seo_settings(){
        return $this->hasMany('App\Models\SeoSetting', 'target_id', 'id')->whereLinkType(2);
    }

    public function dripfeed()
    {
        return $this->hasOne('App\Models\DripFeed', 'target_id', 'id')->whereType('lessons');
    }

    public function getContentAttribute($value)
    {
        return ShortCode::replaceShortcode($value);
    }

    public function discussion_settings(){
        return $this->belongsTo('App\Models\DiscussionSettings');
    }

    public function addAll($videos , $site_id){
        foreach ($videos as $video) {
            $l = new Lesson;
            $l->site_id = $site_id;
            $l->author_id = \Auth::user()->id;
            $l->embed_content = $video['embed_content'];
            $l->featured_image = $video['featured_image'];
            $l->title = $video['title'];
            $l->note = $video['video_length'];
            $l->content = $video['description'] || '';
            $l->type = 'video';
            $l->save();
        }
        $this->site = Site::find($site_id);
        $this->site->total_lessons =  $this->site->total_lessons + count($videos);
        $this->site->save();
        return 'true';
    }

    public function applySearchQuery($q, $value)
    {
        if(!empty($value))
            return $q->where('title', 'like','%' . $value . "%");
        else
            return $q;
    }

    public static function create(array $lesson_data = array())
    {
        $discussions = [];
        $seo = null;
        $dripfeed = null;
        unset($lesson_data['dripfeed']);
        unset($lesson_data['timeLeft']);
        $site_id = $lesson_data["site_id"];
        if (isset($lesson_data["discussion_settings"])) {
            $discussions = $lesson_data["discussion_settings"];
            unset($lesson_data["discussion_settings"]);
        }

        if (isset($lesson_data["seo_settings"])) {
            $seo = $lesson_data["seo_settings"];
            unset($lesson_data["seo_settings"]);
        }

        if (isset($lesson_data['dripfeed_settings']) && !empty($lesson_data['dripfeed_settings']))
        {
            $dripfeed = $lesson_data["dripfeed_settings"];
        }
        unset($lesson_data["dripfeed_settings"]);

        $discussions = DiscussionSettings::create($discussions);
        if (isset($lesson_data['tag']) && $lesson_data['tag'] != '') {
            $module = Module::whereSiteId($site_id)->whereTitle($lesson_data['tag'])->first();
            if (isset($module)) {
                $module_id = $module->id;
            } else {
                $module = Module::create(['site_id' => $site_id, 'title' => $lesson_data['tag']]);
                $module->save();
                $module_id = $module->id;
            }
            $lesson_data['module_id'] = $module->id;
        }
        unset($lesson_data['tag']);
        unset($lesson_data['permalink']);

        $lesson = parent::create($lesson_data);
        $lesson->discussion_settings()->associate($discussions);

        $lesson->save();
        if ($seo){
            SeoSetting::savePage($seo, $site_id, 2 , $lesson->id);
        }

        if ($dripfeed)
        {
            DripFeed::set($lesson, $dripfeed);
        }
    
        return $lesson;

    }

    public function update(array $lesson_data = array()){
        
        unset($lesson_data["subdomain"]);
        unset($lesson_data["dripfeed"]);
        unset($lesson_data['timeLeft']);
        unset($lesson_data['site']);
        $site_id = $lesson_data["site_id"];
        $lesson_id = $lesson_data["id"];

        if (isset($lesson_data["discussion_settings"])){
            $discussions = $lesson_data["discussion_settings"];
            if($this->discussion_settings)
                $this->discussion_settings->update($discussions);
            else{
                $discussions =  DiscussionSettings::create($discussions);
                $this->discussion_settings()->associate($discussions);
                $lesson_data['discussion_settings_id'] = $discussions->id;
            }
            unset($lesson_data["discussion_settings"]);
        }

        if (isset($lesson_data['dripfeed_settings']) && !empty($lesson_data['dripfeed_settings']))
        {
            $dripfeed = $lesson_data['dripfeed_settings'];
            DripFeed::set($this, $dripfeed);
        }
		elseif( !empty( $lesson_data['remove_dripfeed'] ) )
		{
			DripFeed::remove($this);
		}
        unset($lesson_data['dripfeed_settings']);
		unset($lesson_data['remove_dripfeed']);
    
        if (isset($lesson_data["seo_settings"])){
            $seo = $lesson_data["seo_settings"];
            SeoSetting::savePage($seo, $site_id, 2 , $lesson_id);
        }

        unset($lesson_data["seo_settings"]);
        unset($lesson_data['permalink']);
        
        $this->fill($lesson_data);
        $this->save();

        $this->permalink = \App\Models\Permalink::set($this);
        $this->save();

        return $this;
    }

    public static function url_slugify( $str, $replace=array(), $delimiter='-' )
    {
        if( !empty($replace) ) {
            $str = str_replace((array)$replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("#[^a-zA-Z0-9/_|+ -]#", "", $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("#[/_|+ -]+#", $delimiter, $clean);

        return $clean;
    }

    public function getStatistics($model){
        
        $model->next_lesson =  Lesson::whereSiteId($model->site_id)->where('sort_order','>',$model->sort_order)->orderBy('sort_order')->first();
        $model->prev_lesson =  Lesson::whereSiteId($model->site_id)->where('sort_order','<',$model->sort_order)->orderBy('sort_order' , 'desc')->first();

        $access = $this->checkAccess($model , true);
        if($access){
            $model->total_lessons = Lesson::whereSiteId($model->site_id)->count();
            $model->current_index = $model->sort_order;
        }
        else{
            $model->total_lessons = Lesson::whereSiteId($model->site_id)->where('access_level_type','!=',4)->count();
            $model->current_index = Lesson::whereSiteId($model->site_id)->where('access_level_type','!=',4)->where('sort_order','>',$model->sort_order)->orderBy('sort_order')->count();
            $model->current_index = $model->total_lessons - $model->current_index;
        }

        if(!$this->checkAccess($model->next_lesson , true)){
            $model->next_lesson =  Lesson::whereSiteId($model->site_id)->where('access_level_type','!=',4)->where('sort_order','>',$model->sort_order)->orderBy('sort_order')->first();
        }

        if(!$this->checkAccess($model->prev_lesson , true)){
            $model->prev_lesson =  Lesson::whereSiteId($model->site_id)->where('access_level_type','!=',4)->where('sort_order','<',$model->sort_order)->orderBy('sort_order' , 'desc')->first();
        }   
        return $model;
    }

    public function checkAccess($model , $admin){

        if(!$model){
            return true;
        }

        if($model->access_level_type==4 && $admin){
            $site = Site::find($model->site_id);

			if( !empty( $site ) )
            	return (\App\Helpers\SMAuthenticate::set() && \SMRole::hasAccess($site->id,'view_private_content') );
			else
				return false;
        }

        if($admin){
            $site = Site::find($model->site_id);
            return (\App\Helpers\SMAuthenticate::set() && \SMRole::hasAccess($site->id,'view_restricted_content') );
        }
 
        return false;
    }

    public function addDiscussionSettings($model){
        $discussions =  DiscussionSettings::create();
        $model->discussion_settings_id = $discussions->id;
        $model->save();
    }
}

Lesson::created(function($lesson){
    $lesson->site->total_lessons = $lesson->site->total_lessons + 1;
    $lesson->site->save();

    $lesson->permalink = \App\Models\Permalink::set($lesson);
    $lesson->save();

    return $lesson;
});

Lesson::saving(function($lesson){
    \Log::info('I am in saving');

    $keys = array();
    $keys[] ='modules' . ':' . $lesson->site_id . ':*';

    SMCache::clear($keys);

    $routes[] = 'module_home';
    SMCache::reset($routes);
});

Lesson::creating(function($lesson){
    $lesson->author_id = \Auth::user()->id;
    \App\Models\Permalink::handleReservedWords($lesson);
});

Lesson::deleting(function($lesson){
    if(!empty($lesson->id))
    {
        $notesIds = UserNote::where('lesson_id',$lesson->id)->whereNull('deleted_at')->get(['id']);
        if(!empty($notesIds))
            UserNote::destroy(array_pluck($notesIds, 'id'));
    }


    $lesson_key ='modules' . ':' . $lesson->site_id . ':*';

    $keys[] = $lesson_key;
    
    SMCache::clear($keys);

    $routes[] = 'module_home';
    SMCache::reset($routes);
});

/*Lesson::updating(function($lesson){
    //$lesson->permalink = \App\Models\Permalink::set($lesson);
    //$lesson->save();
    $lesson_key ='modules' . ':' . $lesson->site_id . ':*';
    $keys = PRedis::keys($lesson_key);
    foreach ($keys as $key)
    {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
    return $lesson;
});*/
