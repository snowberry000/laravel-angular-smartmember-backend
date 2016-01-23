<?php

namespace App\Models;

use Input;

class Event extends Root
{
    protected $table = "events";
	public $saveable_columns = [ 'site_id', 'event_name', 'user_id', 'email', 'count', 'deleted_at', 'created_at', 'updated_at' ];

	public function meta()
	{
		return $this->hasMany("App\\Models\\EventMetaData", "event_id");
	}

	public static function create(array $data = array())
	{
		$data = Input::only( [ 'site_id', 'event_name', 'user_id', 'email', 'count' ] );
		return parent::create( $data );
	}

	public function update(array $data = array())
	{
		$data = Input::only( [ 'site_id', 'event_name', 'user_id', 'email', 'count' ] );
		return parent::create( $data );
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

			$meta_item->value = $val;
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