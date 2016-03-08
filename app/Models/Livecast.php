<?php namespace App\Models;

class Livecast extends Root
{
    protected $table = 'livecasts';

    public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function access_level()
    {
        return $this->belongsTo('App\Models\AccessLevel', 'access_level_id');
    }

    public function applySearchQuery($q, $value)
    {
        if(!empty($value))
            return $q->where('title', 'like','%' . $value . "%");
        else
            return $q;
    }
    
    public function seo_settings()
    {
        return $this->hasMany('App\Models\SeoSetting', 'target_id', 'id')->whereLinkType(5);
    }

    public function discussion_settings(){
        return $this->belongsTo('App\Models\DiscussionSettings');
    }

    public function dripfeed() {
        return $this->hasOne('App\Models\DripFeed', 'target_id', 'id')->whereType('livecasts');
    }

    public static function create(array $livecast_data = array()){

        $discussions = [];
        $seo = null;
        unset($livecast_data['timeLeft']);
        unset($livecast_data["dripfeed"]);
        $site_id = $livecast_data["site_id"];
        if (isset($livecast_data["discussion_settings"])){
            $discussions = $livecast_data["discussion_settings"];
            unset($livecast_data["discussion_settings"]);
        }
        $discussions =  DiscussionSettings::create($discussions);

        if (isset($livecast_data["seo_settings"])){
            $seo = $livecast_data["seo_settings"];
            unset($livecast_data["seo_settings"]);
        }
        unset($livecast_data['permalink']);

        if (isset($livecast_data['dripfeed_settings']) && !empty($livecast_data['dripfeed_settings']))
        {
            $dripfeed = $livecast_data["dripfeed_settings"];
        }
        unset($livecast_data["dripfeed_settings"]);

        $livecast = parent::create($livecast_data);
        $livecast->discussion_settings()->associate($discussions);
        $livecast->save();
        if ($seo){
            SeoSetting::savePage($seo, $site_id, 5, $livecast->id);
        }

		if ( !empty( $dripfeed ) )
		{
			DripFeed::set($livecast, $dripfeed);
		}
    
        return $livecast;
       
    }

    public function update(array $livecast_data = array()){

        $site_id = $livecast_data["site_id"];
        $page_id = $livecast_data["id"];
        unset($livecast_data['timeLeft']);
        unset($livecast_data["dripfeed"]);
        unset($livecast_data["site"]);
        if (isset($livecast_data["discussion_settings"])){
            $discussions = $livecast_data["discussion_settings"];
            if($this->discussion_settings)
                $this->discussion_settings->update($discussions);
            else{
                $discussions =  DiscussionSettings::create($discussions);
                $this->discussion_settings()->associate($discussions);
                $livecast_data['discussion_settings_id'] = $discussions->id;
            }
            unset($livecast_data["discussion_settings"]);
        }

        if (isset($livecast_data["seo_settings"])){
            $seo = $livecast_data["seo_settings"];
            SeoSetting::savePage($seo, $site_id, 5 , $page_id);
            unset($livecast_data["seo_settings"]);
        }

        if (isset($livecast_data['dripfeed_settings']) && !empty($livecast_data['dripfeed_settings']))
        {
            $dripfeed = $livecast_data['dripfeed_settings'];
            DripFeed::set($this, $dripfeed);
        }
		elseif( !empty( $livecast_data['remove_dripfeed'] ) )
		{
			DripFeed::remove($this);
		}

        unset($livecast_data['dripfeed_settings']);
        unset($livecast_data['remove_dripfeed']);
        unset($livecast_data['permalink']);

        $this->fill($livecast_data);
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

Livecast::creating(function($model){
    \App\Models\Permalink::handleReservedWords($model);
});

Livecast::created(function($model){
    $model->author_id = \Auth::user()->id;

    $model->permalink = \App\Models\Permalink::set($model);
    $model->save();
    return $model;
});

Livecast::updating(function($model){
    //return $model->checkPermalink();
});
