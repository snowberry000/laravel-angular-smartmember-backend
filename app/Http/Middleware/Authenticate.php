<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authorization = explode(" ",\Input::header("Authorization"));

        if (\Input::get('access_token')){
            $this->setAuth(\Input::get("access_token"));
            return $next($request);
        }
        if($authorization && count($authorization) == 2){
            list($type,$access_token) = $authorization;

            switch($type){
                case "Basic":
                        if($this->setAuth($access_token)){
                            return $next($request);
                        }
            }
        }

        \App::abort(401,"A valid access token is required to access the resource");
        return $next($request);
    }

    public function setAuth($access_token){
        $user = \App\Models\User::whereAccessToken($access_token)->first();        
        if($user){
            $this->auth->loginUsingId($user->id);
            return true;
        }
        return false;
    }

}
