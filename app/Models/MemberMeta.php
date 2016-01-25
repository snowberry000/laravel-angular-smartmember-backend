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

	public static function create( array $data = array(), $sm_customer = 10 )
	{
		$attribute = CustomAttribute::whereUserId( $sm_customer )
			->whereName( $data['key'] )->first();

		if( !$attribute )
			$attribute = CustomAttribute::create( [ 'user_id' => $sm_customer, 'name' => $data['key'], 'type' => ( !empty( $data['type'] ) ? $data['type'] : '' ) ] );

		unset( $data['key'] );
		$data['custom_attribute_id'] = $attribute->id;
		$data['member_id'] = \Auth::user()->id;

		$meta_item = self::whereCustomAttributeId( $data['custom_attribute_id'] )->whereMemberId( $data['member_id'] )->first();

		if( $meta_item )
		{
			$meta_item->value = $data['value'];
			$meta_item->save();

			return $meta_item;
		}
		else
		{
			return parent::create( $data );
		}
	}
}