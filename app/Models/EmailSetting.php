<?php

namespace App\Models;
use SMCache;

class EmailSetting extends Root
{
    protected $table = "email_settings";

}

EmailSetting::deleted(function($settings){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $settings;
});

EmailSetting::saving(function($settings){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $settings;
});
