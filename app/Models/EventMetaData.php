<?php

namespace App\Models;

class EventMetaData extends Root
{
    protected $table = "event_metadata";

	public function event()
	{
		return $this->belongsTo('App\Models\Event');
	}
}
