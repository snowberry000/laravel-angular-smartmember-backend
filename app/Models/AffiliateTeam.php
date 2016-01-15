<?php

namespace App\Models;

class AffiliateTeam extends Root
{
    protected $table = "affteams";

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }

    public function members() 
    {
    	return $this->hasMany('App\Models\AffiliateTeamLedger', 'team_id')->with('affiliate');
    }

    public function applySearchQuery($q, $value)
    {
        return $q->where('name', 'like','%' . $value . "%");
    }

    public static function create(array $data = array())
    {
    	if ( isset($data['members']) ) {
    		$members = $data['members'];
    		unset($data['members']);

            \Log::info($data);
    		$affteam = parent::create($data);
    		$teamledger = array();
    		foreach ($members as $member)
    		{
    			$teamledger[] = new AffiliateTeamLedger( ['affiliate_id' => $member,
            											  'team_id' => $affteam->id] );
    		}

    		$affteam->members()->saveMany($teamledger);

    		return $affteam;
    	}

    	return parent::create($data);
    }

    public function update(array $data = array())
    {
        if ( isset($data['members']) ) {
            $members = $data['members'];
            unset($data['members']);
            $this->fill($data);
            $this->save();
            $teamledger = array();
            $this->members()->forceDelete();

            foreach ($members as $member)
            {
                $already_members = $this->members->lists('affiliate_id');
                foreach ($already_members as $already_member) {
                    if($already_member==$member){
                        $exists = true;
                        break;
                    }
                }
                if(!isset($exists))
                $teamledger[] = new AffiliateTeamLedger( ['affiliate_id' => $member,
                                                          'team_id' => $this->id] );
            }
            $this->members()->saveMany($teamledger);

            return $this;
        }
        $this->fill($data);
        $this->save();
        return $this;
    }
}
