<?php namespace App\Models;


class SiteNoticeSeen extends Root
{
    protected $table = 'site_notices_seen';

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function siteNotice(){
        return $this->belongsTo('App\Models\SiteNotice', 'site_notice__id');
    }

}
