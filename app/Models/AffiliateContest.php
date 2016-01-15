<?php

namespace App\Models;
use Carbon\Carbon;

class AffiliateContest extends Root
{
    protected $table = "affcontests";

    public function applySearchQuery($query,$value)
    {
    	return $query->where('title', 'like', '%' . $value . '%');
    }

    public static function create(array $data = array())
    {
        $start_date = Carbon::now();
        $end_date = Carbon::now();

        if ( isset($data['start_date']))
        {
            $date = \DateTime::createFromFormat('j/m/y', $data['start_date']);
            if ($date){
                $start_date = $date->format('Y-m-d');
            }
        }

        if ( isset($data['end_date']))
        {
            $date = \DateTime::createFromFormat('j/m/y', $data['end_date']);
            if ($date){
                $end_date = $date->format('Y-m-d');
            }
        }

    	$contest = parent::create($data);

    	return $contest;
    }
}

AffiliateContest::creating(function($model){
    \Log::info("model");
    \Log::info($model);
	\App\Models\Permalink::handleReservedWords($model);
    $model->user_id = \Auth::user()->id;
});

AffiliateContest::updating(function($model){
    \Log::info("model");
    \Log::info($model);
	\App\Models\Permalink::handleReservedWords($model);
	$model->permalink = \App\Models\Permalink::set($model);
});