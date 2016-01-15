<?php namespace App\Models;


class SiteNotice extends Root
{
    protected $table = 'site_notices';

    public function site(){
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function applySearchQuery($q, $value)
    {
        if(!empty($value))
            return $q->where(function($query) use ($value){
                $query->where('title', 'like','%' . $value . "%")->orwhere('content', 'like','%' . $value . "%");
            });
            //return $q->where('title', 'like','%' . $value . "%")->orwhere('content', 'like','%' . $value . "%");
        else
            return $q;
    }
    
    public function siteNoticeSeen(){
        return $this->hasMany('App\Models\SiteNoticeSeen', 'site_notice_id');
    }
}

SiteNotice::saving(function($notice){
    \SMCache::reset(['siteNotice_getnotifications']);
});

SiteNotice::deleting(function($notice){
    \SMCache::reset(['siteNotice_getnotifications']);
});

