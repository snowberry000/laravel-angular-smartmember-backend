<?php namespace App\Models;

use App\Http\Controllers\Api\SiteController;
use SMCache;

class SiteAds extends Root
{
    protected $table = "sites_ads";

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }
}

SiteAds::deleting(function($ads){

	$routes[] = 'site_details';

	\SMCache::reset($routes);
	return $ads;
});

SiteAds::saving(function($ads){

	$routes[] = 'site_details';

	\SMCache::reset($routes);
	return $ads;
});