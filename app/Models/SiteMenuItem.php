<?php

namespace App\Models;

use App\Http\Controllers\Api\SiteController;
use SMCache;

class SiteMenuItem extends Root
{
    protected $table = "sites_menu_items";

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }
}

SiteMenuItem::deleting(function($data){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $data;
});

SiteMenuItem::saving(function($data){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $data;
});