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
                    $value = $this->verify( $value );
                    Input::merge( array( $key => $this->verifyJsTag( $value ) ) );
                }

            }
        }
        return $next($request);
    }

	public static function allowedScriptPatterns()
	{
		return [
			'[a-z\:\\/\.]*?\.evsuite\.com\\/player[a-z0-9\:\\/\.\?\=\&\-\_]*?',
			'(?:https?:)?\\/\\/app\.voicestak\.com\\/assets\\/js\\/fancybox\.js',
			'(?:https?:)?\\/\\/app\.voicestak\.com\\/assets\\/js\\/voice-stack\.js',
			'(?:https?:)?\\/\\/fast\.wistia\.com\\/[a-z0-9\:\\/\.\?\=\&\-\_]*?'
		];
	}

    /*
        TODO: Add all possible cases for inline javascript here:
    */
    public function verify($value){
		$allowed_patterns = $this->allowedScriptPatterns();

		$all_matches = [];

		foreach( $allowed_patterns as $index => $pattern )
		{
			$matches = [];

			$re = '/<script[^<>]*?src=\"(' . $pattern . ')\"[^<>]*?>.*?<\/script>/is';

			preg_match_all( $re, $value, $matches );

			$all_matches[] = $matches;
		}
		
		foreach( $all_matches as $matches )
		{
			//if we have some matches we need to loop through them to replace them with something else so they don't get stripped out with the rest of the js stuff
			if( !empty( $matches ) && !empty( $matches[ 0 ] ) )
			{
				foreach( $matches[ 0 ] as $key => $val )
				{
					//if we actually have values for the matches we need we are going to switch it out with something else temporarily
					if( !empty( $matches[ 1 ][ $key ] ) )
						$value = str_replace( $val, '@@@@@@@ALLOWEDSCRIPT@@@@@@@' . $matches[ 1 ][ $key ] . '@@@@@@@ALLOWEDSCRIPT@@@@@@@', $value );
				}
			}
		}

        $value = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value);

        $value = str_replace( array('javascript:'), '', $value );

		foreach( $all_matches as $matches )
		{
			if( !empty( $matches ) && !empty( $matches[ 0 ] ) )
			{
				foreach( $matches[ 0 ] as $key => $val )
				{
					//if this was something we had matches for earlier we should be able to change it back to some acceptable js
					if( !empty( $matches[ 1 ][ $key ] ) )
						$value = str_replace( '@@@@@@@ALLOWEDSCRIPT@@@@@@@' . $matches[ 1 ][ $key ] . '@@@@@@@ALLOWEDSCRIPT@@@@@@@', '<script type="text/javascript" src="' . $matches[ 1 ][ $key ] . '"></script>', $value );
				}
			}
		}

        return $value;
    }

	public function verifyJsTag($value){
		$allowed_patterns = $this->allowedScriptPatterns();

		$all_matches = [];

		foreach( $allowed_patterns as $index => $pattern )
		{
			$matches = [];

			$re = '/<javascript[^<>]*?src=\"(' . $pattern . ')\"[^<>]*?>.*?<\/javascript>/is';

			preg_match_all( $re, $value, $matches );

			$all_matches[] = $matches;
		}

		foreach( $all_matches as $matches )
		{
			//if we have some matches we need to loop through them to replace them with something else so they don't get stripped out with the rest of the js stuff
			if( !empty( $matches ) && !empty( $matches[ 0 ] ) )
			{
				foreach( $matches[ 0 ] as $key => $val )
				{
					//if we actually have values for the matches we need we are going to switch it out with something else temporarily
					if( !empty( $matches[ 1 ][ $key ] ) )
						$value = str_replace( $val, '@@@@@@@ALLOWEDSCRIPT@@@@@@@' . $matches[ 1 ][ $key ] . '@@@@@@@ALLOWEDSCRIPT@@@@@@@', $value );
				}
			}
		}

		$value = preg_replace('#<javascript(.*?)>(.*?)</javascript>#is', '', $value);

		foreach( $all_matches as $matches )
		{
			if( !empty( $matches ) && !empty( $matches[ 0 ] ) )
			{
				foreach( $matches[ 0 ] as $key => $val )
				{
					//if this was something we had matches for earlier we should be able to change it back to some acceptable js
					if( !empty( $matches[ 1 ][ $key ] ) )
						$value = str_replace( '@@@@@@@ALLOWEDSCRIPT@@@@@@@' . $matches[ 1 ][ $key ] . '@@@@@@@ALLOWEDSCRIPT@@@@@@@', '<javascript type="text/javascript" src="' . $matches[ 1 ][ $key ] . '"></javascript>', $value );
				}
			}
		}

		return $value;
	}
}
