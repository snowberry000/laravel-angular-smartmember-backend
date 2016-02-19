<?php

namespace App\Helpers;

use PRedis;
use Domain;

class CacheHelper
{

	public static function clear( $keys = [ ] )
	{
		foreach( $keys as $key )
		{
			$redis_keys = PRedis::keys( $key );
			foreach( $redis_keys as $redis_key )
			{
				PRedis::del( $redis_key );
			}
		}
	}

	public static function reset( $routes = [ ] )
	{
		$subdomain = DomainHelper::getSubdomain();
		$domain = DomainHelper::getDomain();
		$keys      = [ ];
		foreach( $routes as $route )
		{

			if( $route == 'user_' && \Auth::check() )
				$route = $route . \Auth::user()->id;

			$keys[] = $subdomain . ':_' . $route;
			$keys[] = $subdomain . ':_' . $route . ':*';

			if( $domain )
			{
				$keys[] = $subdomain . ':_' . $route;
				$keys[] = $subdomain . ':_' . $route . ':*';
			}
		}
		self::clear( $keys );
	}

	public static function shouldCache()
	{
		$cached_actions = \Config::get( 'cache.actions' );
		$request_uri    = self::getFormattedRequest();
		//dd($request_uri);
		foreach( $cached_actions as $action )
		{
			if( preg_match( '/_' . $action . '/', $request_uri ) )
			{
				//dd($action);
				return true;
			}
		}
		return false;
	}

	public static function getKey()
	{
		$subdomain   = Domain::getSubdomain();
		$request_uri = self::getFormattedRequest();
		$key         = $subdomain . ":" . $request_uri;

		if( \Input::header( "Authorization" ) )
		{
			$authorization = explode( " ", \Input::header( "Authorization" ) );
			list( $type, $access_token ) = $authorization;

			$key .= ":" . $access_token;
		}
		return $key;
	}

	public static function getDomainKey()
	{
		$domain = Domain::getDomain();

		if( $domain )
		{
			$request_uri = self::getFormattedRequest();
			$key         = $domain . ":" . $request_uri;

			if( \Input::header( "Authorization" ) )
			{
				$authorization = explode( " ", \Input::header( "Authorization" ) );
				list( $type, $access_token ) = $authorization;

				$key .= ":" . $access_token;
			}

			return $key;
		}

		return false;
	}

	public static function getFormattedRequest()
	{
		$request_uri = $_SERVER[ "REQUEST_URI" ];
		$request_uri = explode( "?", $request_uri );
		$request_uri = str_replace( "/", "_", array_shift( $request_uri ) );

		return $request_uri;
	}


}