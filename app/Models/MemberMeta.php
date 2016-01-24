<?php

namespace App\Models;

class MemberMeta extends Root
{
    protected $table = "member_meta";

	public function member()
	{
		return $this->belongsTo('App\Models\User', 'member_id');
	}
}

MemberMeta::saving( function( $meta_item ) {
	if( \Input::has('sm_customer_id') )
	{
		
	}
} );