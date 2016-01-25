<?php

namespace App\Models;

use App\Http\Controllers\Api\SiteController;
use App\Helpers\SMAuthenticate;
use SMCache;

class SiteMetaData extends Root
{
    protected $table = "site_meta_data";

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }
    public function template(){
        return $this->hasOne('App\Models\Template', "data_type_id");
    }
    public function type(){
        return $this->belongsTo('App\Models\MetaDataType', "data_type");
    }

    public function menu_items(){
        return $this->hasMany('App\Models\SiteMenuItem', "site_id");
    }

	public static function clearHomepageCache( $data )
	{
		if( $data->key == 'homepage_url' && isset($_SERVER['HTTP_HOST']) )
		{
			$domain = $_SERVER[ 'HTTP_HOST' ];
			$parts = explode( ".", $domain );
			$tld = array_pop( $parts );

			$site = Site::find( $data->site_id );
			if( $site )
			{
				$keys = [];
				$keys[] = $site->subdomain . '.smartmember.' . $tld . '::*';

				if( !empty( $site->domain ) )
					$keys[] = $site->domain . '::*';

				SMCache::clear($keys);
			}
		}
	}
}

SiteMetaData::deleting(function($data){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $data;
});

SiteMetaData::saving(function($data){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';

    SMCache::reset($routes);

	SiteMetaData::clearHomepageCache($data);

	$original_value = $data->getOriginal( 'value' );
	if( $data->value != $original_value )
	{
		if( SMAuthenticate::set() )
			$user_id = \Auth::user()->id;
		else
			$user_id = 0;

		$log_change = false;

		$items_to_log = [
			'site_logo',
			'favicon',
			'homepage_url',
			'facebook_retargetting_pixel',
			'facebook_conversion_pixel',
			'site_background_image'
		];

		if( in_array( $data->key, $items_to_log ) )
			$log_change = true;

		if( $log_change )
		{
			\App\Models\Event::Log( 'updated-' . str_replace( '_', '-', $data->key ), array(
				'site_id' => $data->site_id,
				'user_id' => $user_id,
				'previous-value' => $original_value,
				'new-value' => $data->value
			) );
		}
	}

    return $data;
});