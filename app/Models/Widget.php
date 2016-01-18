<?php namespace App\Models;

use App\Http\Controllers\Api\SiteController;
use SMCache;

class Widget extends Root
{
    protected $table = "widgets";

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }

	public function banner(){
		return $this->hasOne('App\Models\SiteAds', 'id', 'target_id' );
	}

	public function meta_data(){
		return $this->hasMany('App\Models\WidgetMeta');
	}
}

Widget::deleting(function($widget){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    \SMCache::reset($routes);
    return $widget;
});

Widget::saving(function($widget){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    \SMCache::reset($routes);
    return $widget;
});