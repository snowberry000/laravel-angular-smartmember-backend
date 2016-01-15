<?php

namespace App\Models;

use App\Http\Controllers\Api\SiteController;
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
		if( $data->key == 'homepage_url' )
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
    return $data;
});