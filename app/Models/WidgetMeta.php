<?php

namespace App\Models;

class WidgetMeta extends Root
{
	protected $table = "widget_meta";

	public function widget()
	{
		return $this->belongsTo( 'App\Models\Widget' );
	}

	public static function set( $model, array $data = array(), $delete_extras = true )
	{
		$meta_ids = [];
		if( $data )
		{
			foreach( $data as $key => $value )
			{
				$meta = WidgetMeta::whereWidgetId( $model->id )->whereKey( $key )->first();
				if( !$meta )
				{
					$meta = new WidgetMeta();
				}
				$meta->key       = $key;
				$meta->value     = $value;
				$meta->widget_id = $model->id;
				$meta->save();

				$meta_ids[] = $meta->id;
			}
		}

		if( $delete_extras )
		{
			$extra_items = self::whereWidgetId( $model->id );

			if( !empty( $meta_ids ) )
				$extra_items = $extra_items->whereNotIn( 'id', $meta_ids );

			$extra_items = $extra_items->get();

			foreach( $extra_items as $extra_item )
				$extra_item->delete();
		}
	}
}
