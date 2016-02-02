<?php namespace App\Models;

class Download extends Root
{
    protected $table = 'download_center';

    public function site()
    {
        return $this->belongsTo('App\Models\Site');
    }

    public function access_level(){
        return $this->belongsTo('App\Models\AccessLevelType', 'access_level');
    }

    public function access_level_type(){
        return $this->belongsTo('App\Models\AccessLevelType', 'access_level_type');
    }

    public function history()
    {
        return $this->hasMany('App\Models\DownloadHistory', "download_id");
    }

    public function media_item()
    {
        return $this->belongsTo('App\Models\MediaItem');
    }

    public function dripfeed()
    {
        return $this->hasOne('App\Models\DripFeed', 'target_id', 'id')->whereType('download_center');
    }


    public function history_count()
    {
        return $this->history()
            ->selectRaw('download_id, count(*) as count')
            ->groupBy('download_id');
    }

    public function unique_count()
    {
        return $this->history()
            ->selectRaw('count(distinct(user_id)) as unique_count, download_id')
            ->groupBy('download_id');
    }

    public function seo_settings()
    {
        return $this->hasMany('App\Models\SeoSetting', 'target_id', 'id')->whereLinkType(3);
    }

    public static function create(array $download_data = array()){
        $seo = null;
        $media = null;
        $dripfeed = null;
        unset($download_data['dripfeed']);
        unset($download_data['timeLeft']);
        $site_id = $download_data["site_id"];
    
        if (isset($download_data["seo_settings"])){
            $seo = $download_data["seo_settings"];
            unset($download_data["seo_settings"]);
        }

        if (isset($download_data['dripfeed_settings']) && !empty($download_data['dripfeed_settings']))
        {
            $dripfeed = $download_data["dripfeed_settings"];
        }
        unset($download_data["dripfeed_settings"]);
        
		if (isset($download_data["media_item"]))
            unset($download_data["media_item"]);

        unset($download_data['permalink']);

        $download = parent::create($download_data);

        $download->save();
        
        if ($seo){
            SeoSetting::savePage($seo, $site_id, 3 , $download->id);
        }

        if ($dripfeed) {
            DripFeed::set($download, $dripfeed);
        }
    
        return $download;

    }

    public function applySearchQuery($q, $value)
    {
        return $q->where('title', 'like','%' . $value . "%");
    }


    public function update(array $download_data = array()){
    
        $site_id = $download_data["site_id"];
        $download_id = $download_data["id"];
        unset($download_data['dripfeed']);
        unset($download_data['timeLeft']);
        unset($download_data['site']);
        if (isset($download_data["seo_settings"])){
            $seo = $download_data["seo_settings"];
            SeoSetting::savePage($seo, $site_id, 3 , $download_id);
            unset($download_data["seo_settings"]);
        }

        if( isset( $download_data["media_item"] ) )
            unset( $download_data["media_item"] );

        if(isset($download_data['dripfeed_settings']) && !empty($download_data['dripfeed_settings']))
        {
            $dripfeed = $download_data['dripfeed_settings'];
            DripFeed::set($this, $dripfeed);
        }
		elseif( !empty( $download_data['remove_dripfeed'] ) )
		{
			DripFeed::remove($this);
		}
		unset($download_data['remove_dripfeed']);
        unset($download_data['dripfeed_settings']);
        unset($download_data['permalink']);

        $this->fill($download_data);
        $this->save();
        
        $this->permalink = \App\Models\Permalink::set($this);
        $this->save();

        return $this;
    } 
}


Download::created(function($model){
    $model->creator_id = \Auth::user()->id;

    $model->permalink = \App\Models\Permalink::set($model);
    $model->save();
    return $model;
});

Download::creating(function($model){
    \App\Models\Permalink::handleReservedWords($model);
});


Download::updating(function($model){
    return $model->checkPermalink();
});
