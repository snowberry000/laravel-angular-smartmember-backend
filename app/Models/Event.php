<?php

namespace App\Models;

use Input;
use App\Helpers\SMAuthenticate;
use App\Helpers\DomainHelper;

class Event extends Root
{
    protected $table = "events";
	protected $with = ['meta'];
	public $saveable_columns = [ 'site_id', 'event_name', 'user_id', 'email', 'count', 'company_id', 'deleted_at', 'created_at', 'updated_at' ];

	public function meta()
	{
		return $this->hasMany("App\\Models\\EventMetaData", "event_id");
	}

	public static function create(array $data = array())
	{
		$data = Input::only( [ 'site_id', 'event_name', 'user_id', 'email', 'count', 'company_id' ] );
		return parent::create( $data );
	}

	public function update(array $data = array())
	{
		$data = Input::only( [ 'site_id', 'event_name', 'user_id', 'email', 'count' ] );
		return parent::create( $data );
	}

	public static function applySearchQuery( $query, $value )
	{
		return $query->where( function ( $q ) use ( $value )
		{
			$q->where( 'event_name', 'like', '%' . $value . '%' );
			$q->orwhere( 'email', 'like', '%' . $value . '%' );
		} );
	}

	public static function Log( $event_name, $data = array() )
	{
		if( SMAuthenticate::set() && empty( $data['user_id'] ) )
			$data['user_id'] =  \Auth::user()->id;

		if( empty( $data['site_id'] ) )
		{
			$site = DomainHelper::getSite();

			if( $site )
				$data[ 'site_id' ] = $site->id;
		}

		$important_data = [ 'site_id' => 0, 'event_name' => $event_name, 'user_id' => 0, 'email' => '', 'count' => 0 ];

		foreach( $important_data as $key => $val )
		{
			if( isset( $data[ $key ] ) )
			{
				$important_data[ $key ] = $data[ $key ];
				unset( $data[ $key ] );
			}
		}

		if( !empty( $important_data['event_name'] ) && ( !empty( $important_data['user_id'] ) || !empty( $important_data['email'] ) ) )
		{
			$model = self::whereEventName( $important_data['event_name'] );

			if( !empty( $important_data['site_id'] ) )
				$model = $model->whereSiteId( $important_data['site_id'] );
			else
				$model = $model->whereSiteId( 0 );

			if( !empty( $important_data['user_id'] ) )
				$model = $model->whereUserId( $important_data['user_id'] )->first();
			else
				$model = $model->whereEmail( $important_data['email'] )->first();

			if( !$model )
				$model = parent::create( $important_data );
		}

		if( !empty( $model ) )
		{
			if( !$model->count )
				$model->count = 1;
			else
				$model->count = $model->count + 1;

			$model->save();

			$keys_used = [];

			foreach( $data as $key => $val )
			{
				if( !in_array( $key, $model->saveable_columns ) )
				{
					$keys_used[] = $key;
					$meta_item = EventMetaData::whereEventId( $model->id )->whereKey( $key )->first();

					if( !$meta_item )
					{
						// $meta_item = EventMetaData::create([
						//    'event_id' => $model->id,
						//    'key' => $key
					 //   	]);
					}

					$meta_item->value = is_array( $val ) || is_object( $val ) ? json_encode( $val ) : $val;
					$meta_item->save();
				}
			}

			$extra_meta = EventMetaData::whereEventId( $model->id );

			if( !empty( $keys_used ) )
				$extra_meta = $extra_meta->whereNotIn( 'key', $keys_used );

			$extra_meta = $extra_meta->get();

			foreach( $extra_meta as $key => $val )
				$val->forceDelete();
		}
	}
}

Event::saved( function( $model ) {
	$keys_used = [];

	foreach( Input::all() as $key => $val )
	{
		if( !in_array( $key, $model->saveable_columns ) )
		{
			$keys_used[] = $key;
			$meta_item = EventMetaData::whereEventId( $model->id )->whereKey( $key )->first();

			if( !$meta_item )
			{
				$meta_item = EventMetaData::create([
					'event_id' => $model->id,
					'key' => $key
			    ]);
			}

			$meta_item->value = is_array( $val ) || is_object( $val ) ? json_encode( $val ) : $val;
			$meta_item->save();
		}
	}

	$extra_meta = EventMetaData::whereEventId( $model->id );

	if( !empty( $keys_used ) )
		$extra_meta = $extra_meta->whereNotIn( 'key', $keys_used );

	$extra_meta = $extra_meta->get();

	foreach( $extra_meta as $key => $val )
		$val->forceDelete();
});