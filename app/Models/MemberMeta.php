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

		$type = self::DetectType( $data['value'] );

		if( !$attribute )
			$attribute = CustomAttribute::create( [ 'user_id' => $sm_customer, 'name' => $data['key'], 'type' => ( !empty( $data['type'] ) ? $data['type'] : $type ) ] );
		else
			$attribute->UpdateType( $type );

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

	public static function DetectType( $value )
	{
		if( self::is_date( $value ) )
			return 'date';
		elseif( is_numeric( $value ) )
			return 'number';
		else
			return 'string';
	}

	public static function is_date( $date )
	{
		$allowed_formats = [
			'Y-m-d',
			'Y-m-d H:i:s',
			'd/m/Y',
			'H:i',
			'm/d/Y',
			'y-m-d',
			'Y-m-d',
			'm/d/y',
			'd/m/y',
			"F j, Y, g:i a",
			"m.d.y",
			"j, n, Y",
			"D M j G:i:s T Y",
			"H:i:s"
		];

		foreach( $allowed_formats as $key => $val )
		{
			$d = \DateTime::createFromFormat( $val, $date );
			if( $d && $d->format( $val ) == $date )
				return true;
		}

		return false;
	}

	public static function get( $key, $user_id, $sm_customer = 10 )
	{
		$attribute = CustomAttribute::whereUserId( $sm_customer )
			->whereName( $key )->first();

		if( !$attribute )
			return false;

		return self::whereCustomAttributeId( $attribute->id )->whereMemberId( $user_id )->first();
	}
}