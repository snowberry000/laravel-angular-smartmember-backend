<?php

namespace App\Models;

use App\Http\Controllers\Api\SiteController;

class Wizard extends Root
{
    protected $table = "wizards";

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }

    public static function customCreate(array $data = array())
	{
		if( empty( $data['site_id'] ) )
		{
			$site = \Domain::getSite();

			if( !empty( $site ) )
				$data['site_id'] = $site->id;
		}

		if( isset( $data[ 'site_id' ] ) )
			$wizard = Wizard::firstOrCreate( [ 'slug' => $data[ 'slug' ], 'site_id' => $data[ 'site_id' ] ] );

		if( !empty( $wizard ) )
		{
			$completed_nodes       = explode( ",", $data[ 'completed_nodes' ] );
			$saved_completed_nodes = explode( ",", $wizard->completed_nodes );

			if( count( $completed_nodes ) >= count( $saved_completed_nodes ) )
			{
				$wizard->completed_nodes = $data[ 'completed_nodes' ];
				$wizard->options         = isset( $data[ 'options' ] ) ? $data[ 'options' ] : null;
				$wizard->save();
			}

			return $wizard;
		}

		return false;
    }
}