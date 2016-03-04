<?php namespace App\Models;

use App\Models\ShortCode;

class CustomPage extends Root
{
    protected $table = 'custom_pages';

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }
    public function access_level()
    {
        return $this->belongsTo('App\Models\AccessLevel', 'access_level_id');
    }

    public function access_level_type(){
        return $this->belongsTo('App\Models\AccessLevelType', 'access_level_type');
    }

    public function discussion_settings(){
        return $this->belongsTo('App\Models\DiscussionSettings');
    }
    
    public function seo_settings(){
        return $this->hasMany('App\Models\SeoSetting', 'target_id', 'id')->whereLinkType(1);
    }

    public function getContentAttribute($value)
    {
        return ShortCode::replaceShortcode($value);
    }

	public function applySearchQuery($q, $value)
	{
		return $q->where('title', 'like','%' . $value . "%");
	}
    
    public static function create(array $page_data = array()){

        $discussions = [];
        $permalink_data = null;
        $seo = null;
        unset($page_data['site']);
        $site_id = $page_data["site_id"];
        if (isset($page_data["discussion_settings"])){
            $discussions = $page_data["discussion_settings"];
            unset($page_data["discussion_settings"]);
        }
        $discussions =  DiscussionSettings::create($discussions);

        if (isset($page_data["seo_settings"])){
            $seo = $page_data["seo_settings"];
            unset($page_data["seo_settings"]);
        }
        unset($page_data['permalink']);
        
        $page = parent::create($page_data);
        $page->discussion_settings()->associate($discussions);
        $page->save();
        if ($seo){
            SeoSetting::savePage($seo, $site_id, 1, $page->id);
        }
        if ($permalink_data){
            Permalink::savePage($permalink_data, $site_id, 1, $page->id);
        }
    
        return $page;
       
    }

    public function update(array $page_data = array()){

        $site_id = $page_data["site_id"];
        $page_id = $page_data["id"];
        unset($page_data['site']);
        if (isset($page_data["discussion_settings"])){
            $discussions = $page_data["discussion_settings"];
            if($this->discussion_settings)
                $this->discussion_settings->update($discussions);
            else{
                $discussions =  DiscussionSettings::create($discussions);
                $this->discussion_settings()->associate($discussions);
                $page_data['discussion_settings_id'] = $discussions->id;
            }
            
            unset($page_data["discussion_settings"]);
        }

        if (isset($page_data["seo_settings"])){
            $seo = $page_data["seo_settings"];
            SeoSetting::savePage($seo, $site_id, 1, $page_id);
            unset($page_data["seo_settings"]);
        }
        unset($page_data['permalink']);
        $this->fill($page_data);
        $this->save();

        $this->permalink = \App\Models\Permalink::set($this);
        $this->save();

        return $this;

    }

    public function addDiscussionSettings($model){
        $discussions =  DiscussionSettings::create();
        $model->discussion_settings_id = $discussions->id;
        $model->save();
    }
}

CustomPage::creating(function($model){
    \App\Models\Permalink::handleReservedWords($model);
});

CustomPage::created(function($model){
    $model->permalink = \App\Models\Permalink::set($model);
    $model->save();
    return $model;
});

CustomPage::updating(function($model){
   
    return $model;
});
