<?php

namespace App\Models;

class AffiliateTeamLedger extends Root
{
    protected $table = "affteamledger";
    // protected $with = ['affiliate'];

    public function team() 
    {
    	return $this->belongsTo('App\Model\AffiliateTeam');
    }
    public function site()
    {
        return $this->belongsTo('App\Models\Site');
    }

    public function affiliate()
    {
    	return $this->belongsTo('App\Models\Affiliate');
        
    }
}
