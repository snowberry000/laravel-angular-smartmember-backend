<?php

namespace App\Models;

use App\Helpers\SMAuthenticate;

class MemberMeta extends Root
{
    protected $table = "member_meta";

	public function member()
	{
		return $this->belongsTo('App\Models\User', 'member_id');
	}
}

MemberMeta::saving( function( $meta_item ) {
	if( \Input::has('sm_customer_id') && !empty( \Input::get('sm_customer_id') ) && \Input::has('key') && !empty( \Input::get('key') ) )
	{
		$attribute = CustomAttribute::whereUserId( \Input::get('sm_customer_id') )->whereName( \Input::get('key') )->first();

		if( !$attribute )
			$attribute = CustomAttribute::create(['user_id' => \Input::get('sm_customer_id'), 'name' => \Input::get('key'), 'type' => \Input::get('type', '')]);

		$meta_item->custom_attribute_id = $attribute->id;

		if( SMAuthenticate::set() )
			$meta_item->member_id = \Auth::user()->id;
	}
} );