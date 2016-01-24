<?php

namespace App\Models;

class MemberMeta extends Root
{
    protected $table = "member_meta";

	public function event()
	{
		return $this->belongsTo('App\Models\Event');
	}
}
