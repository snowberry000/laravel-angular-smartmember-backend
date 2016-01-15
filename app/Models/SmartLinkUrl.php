<?php

namespace App\Models;

class SmartLinkUrl extends Root
{
    protected $table = "smart_link_urls";
	protected $guarded = ['visits'];

	public function smart_link()
	{
		return $this->belongsTo('App\Models\SmartLink', 'smart_link_id');
	}
}
