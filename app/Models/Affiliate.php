<?php

namespace App\Models;

class Affiliate extends Root
{
    protected $table = "affiliates";

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }
    public function applySearchQuery($query, $value)
    {
        return $query->where(function($q) use ($value){
			$q->where('user_name', 'like', '%' . $value . '%');
			$q->orwhere('user_email', 'like', '%' . $value . '%');
		});
    }
}
