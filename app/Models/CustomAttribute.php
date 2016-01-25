<?php

namespace App\Models;

class CustomAttribute extends Root
{
    protected $table = "custom_attributes";

	public function event()
	{
		return $this->belongsTo('App\Models\Event');
	}
}
