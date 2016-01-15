<?php namespace App\Models;

use App\Models\BridgePage\SwapSpot;
use PRedis;
use SMCache;

class BridgePage extends Root
{
    protected $table = 'bridge_bpages';

    public function template() {
        return $this->belongsTo('App\Models\BridgePage\BridgeTemplate', 'template_id');
    }

    public function seo_settings(){
        return $this->hasMany('App\Models\SeoSetting', 'target_id', 'id')->whereLinkType(6);
    }

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }

    public function swapspots()
    {
        return $this->hasMany('App\Models\BridgePage\SwapSpot', 'bridge_page_id');
    }

    public static function create(array $page_data = array()){

        $permalink_data = null;
        $seo = null;
        $swapspots = null;

        $site_id = $page_data["site_id"];
        unset($page_data['access_level_type']);
        if (isset($page_data["seo_settings"])){
            $seo = $page_data["seo_settings"];
            unset($page_data["seo_settings"]);
        }

        if (isset($page_data["swapspot"])){
            $swapspots = $page_data["swapspot"];
            unset($page_data["swapspot"]);
        }

        unset($page_data['permalink']);
        $page = parent::create($page_data);
        $page->save();
        if ($seo){
            SeoSetting::savePage($seo, $site_id, 6, $page->id);
        }
        if ($swapspots)
        {
            SwapSpot::savePage($swapspots, $site_id, $page->id);
        }
        if ($permalink_data){
            Permalink::savePage($permalink_data, $site_id, 6, $page->id);
        }

        return $page;

    }

    public function update(array $page_data = array()){

        $site_id = $page_data["site_id"];
        $page_id = $page_data["id"];
        unset($page_data['access_level_type']);
        unset($page_data['swapspots']);
        unset($page_data['template']);

        if (isset($page_data["seo_settings"])){
            $seo = $page_data["seo_settings"];
            SeoSetting::savePage($seo, $site_id, 6, $page_id);
            unset($page_data["seo_settings"]);
        }

        if (isset($page_data["swapspot"])){
            $swapspots = $page_data["swapspot"];
            SwapSpot::savePage($swapspots, $site_id, $page_id);
            unset($page_data["swapspot"]);
        }
        unset($page_data['permalink']);
        $this->fill($page_data);
        $this->save();

        $this->permalink = \App\Models\Permalink::set($this);
        $this->save();

        return $this;
    }

	public static function clearHomepageCache( $site_id )
	{
			$domain = $_SERVER[ 'HTTP_HOST' ];
			$parts = explode( ".", $domain );
			$tld = array_pop( $parts );

			$site = Site::find( $site_id );
			if( $site )
			{
				$keys = [];
				$keys[] = $site->subdomain . '.smartmember.' . $tld . '::*';

				if( !empty( $site->domain ) )
					$keys[] = $site->domain . '::*';

				SMCache::clear($keys);
			}
	}

	public static function clearCache( $model )
	{
		$domain = $_SERVER[ 'HTTP_HOST' ];
		$parts = explode( ".", $domain );
		$tld = array_pop( $parts );

		$site = Site::find( $model->site_id );
		if( $site )
		{
			$keys = [];
			$keys[] = $site->subdomain . '.smartmember.' . $tld . '::*';

			if( !empty( $site->domain ) )
				$keys[] = $site->domain . '::*';

			$keys[] = $site->subdomain . '.smartmember.' . $tld . ':' . $model->permalink . ':*';

			if( !empty( $site->domain ) )
				$keys[] = $site->domain . ':' . $model->permalink . ':*';

			SMCache::clear($keys);
		}
	}
}

BridgePage::created(function($model){
    $model->permalink = \App\Models\Permalink::set($model);
    $model->save();
    return $model;
});

BridgePage::saved(function($model){
	BridgePage::clearCache($model);
});

BridgePage::deleting(function($model){
	BridgePage::clearCache($model);
});



