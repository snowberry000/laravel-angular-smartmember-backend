<?php

namespace App\Models;

use App\Http\Controllers\Api\SiteController;
use SMCache;

class SiteFooterMenuItem extends Root
{
    protected $table = "sites_footer_menu_items";

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }
}

SiteFooterMenuItem::deleting(function($footer_item){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $footer_item;
});

SiteFooterMenuItem::saving(function($footer_item){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $footer_item;
});