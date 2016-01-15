<?php

namespace App\Http\Middleware;

use Input;
use Closure;

class ScriptCheck
{
    /**
     * Create a new filter instance.
     *
     * @return void
     */
    public function __construct(){

    }

    public function handle($request, Closure $next)
    {
        //currently we are getting rid of stuff from the input data, we probably want to modify it to only clean it out of stuff being saved to the db
        //there are instances where we want to scrape stuff from javascript code.

        //this was a major cludge as I don't know how to turn the filter off for just one function -Todd
        if( !empty( Input::get('url') ) && strpos( Input::get('url'), 'jvzoo' ) !== false && !empty( Input::get('type') ) && Input::get('type') == 'jvzoo' && !empty( Input::get('content') ) )
        {

        }
        else
        {
            foreach( Input::all() as $key => $value )
            {
                if( is_string( $value ) )
                {
                    Input::merge( array( $key => $this->verify( $value ) ) );
                }

            }
        }
        return $next($request);
    }

    /*
        TODO: Add all possible cases for inline javascript here:
    */
    public function verify($value){
        $value = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value);

        $value = str_replace( array('javascript:'), '', $value );

        return $value;
    }
}
