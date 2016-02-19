<?php

namespace App\Http\Middleware;

use Closure;
use Domain;
use PRedis;
use SMCache;

class BeforeGetRequest
{
    public function handle($request, Closure $next)
    {
    	if ($_SERVER["REQUEST_METHOD"] != "GET"){
    		return $next($request);
    	}
       	$key = SMCache::getKey();     
    	$data = PRedis::get($key);
    	if ($data){
    		return response()->json(json_decode(PRedis::get($key)));
    	}
    	return $next($request);
    }

}
