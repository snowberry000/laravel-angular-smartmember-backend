<?php

namespace App\Http\Middleware;

use Closure;
use PRedis;
use SMCache;


class AfterGetRequest
{   

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (!SMCache::shouldCache() || $_SERVER["REQUEST_METHOD"] != "GET"){
            return $response;
        }    	


        $key = SMCache::getKey();
		$domain_key = SMCache::getDomainKey();
		if( $domain_key )
			PRedis::setex( $domain_key,30*60, json_encode($response->original));

        //TODO: FOr access token store for just 15 minutes, else 24 hours
        PRedis::setex($key,30*60, json_encode(isset($response->original) ? $response->original : ''));
        return $response;
    }

   


}