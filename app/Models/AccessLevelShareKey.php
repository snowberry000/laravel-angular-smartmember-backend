<?php namespace App\Models;

use App\Models\AccessLevel\Grant;
use App\Models\AccessLevel\Pass;
use App\Models\AccessLevel;
use SMCache;
class AccessLevelShareKey extends Root
{
    protected $table = 'access_level_shared_keys';

    public function access_level()
    {
        return $this->belongsTo('App\Models\AccessLevel', 'access_level_id');
    }

    public function originate_site()
    {
        return $this->belongsTo('App\Models\Site', 'originate_site_id');
    }

    public function destination_site()
    {
        return $this->belongsTo('App\Models\Site', 'destination_site_id');
    }

    public function applySearchQuery($query, $value)
    {
        return $query->where('key', 'like', '%' . $value . "%");
    }
}