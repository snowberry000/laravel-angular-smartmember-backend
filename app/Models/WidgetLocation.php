<?php

namespace App\Models;

class WidgetLocation extends Root
{
    protected $table = "widget_locations";

    public function widget()
	{
        return $this->belongsTo('App\Models\Widget');
    }

	public static function set( $model, array $data = array(), $delete_extras = true )
	{
		$location_ids = [];
		if( $data )
		{
			foreach( $data as $key => $value )
			{
				$type_info = explode( '_', $value );

				$location = self::whereWidgetId( $model->id )->whereType( $type_info[0] );

				if( !empty( $type_info[1] ) )
					$location = $location->whereTarget( $type_info[1] );

				$location = $location->first();

				if( !$location )
				{
					$location = new WidgetLocation();
				}

				$location->type       = $type_info[0];
				$location->target     = empty( $type_info[1] ) ? null : $type_info[1];
				$location->widget_id = $model->id;
				$location->save();

				if( $location->target == 'all' )
				{
					$extra_items = self::whereWidgetId( $model->id )->whereType( $location->type )->where( 'target', '!=', 'all' )->get();

					foreach( $extra_items as $extra_item )
						$extra_item->delete();
				}

				if( $location->type == 'everywhere' )
				{
					$extra_items = self::whereWidgetId( $model->id )->where( 'type', '!=', 'everywhere' )->get();

					foreach( $extra_items as $extra_item )
						$extra_item->delete();
				}

				$location_ids[] = $location->id;
			}
		}

		if( $delete_extras )
		{
			$extra_items = self::whereWidgetId( $model->id );

			if( !empty( $location_ids ) )
				$extra_items = $extra_items->whereNotIn( 'id', $location_ids );

			$extra_items = $extra_items->get();

			foreach( $extra_items as $extra_item )
				$extra_item->delete();
		}
	}
}
