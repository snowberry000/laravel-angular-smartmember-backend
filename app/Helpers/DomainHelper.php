<?php

namespace App\Helpers;

use Request;
use App\Models\Site;
use App\Models\Role;

class DomainHelper
{
    public static function root()
    {
        $domain = explode(".", isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : 'smartmember.com');
        $root = array_pop($domain);
        $root = array_pop($domain) . "." . $root;
        return $root;
    }

    public static function appRoute($subdomain, $route, $isAdmin = true)
    {
        $path = \Config::get('app.protocol') . $subdomain . "." . self::root();
        $path .= $route;

        return $path;
    }

	public static function isCustomDomain( $domain )
	{
		return !preg_match( '/^(?:http(?:s)?\:)?(?:\/\/)?(?:[a-z0-9\-]{1,63})?\.smartmember\.(?:com|in|dev|soy|pro|co)(?:\/(?:.*)?)?$/i', $domain );
	}

	public static function domainParts( $domain )
	{
		preg_match( '/^(?:http(?:s)?\:)?(?:\/\/)?([a-z0-9\-]{1,63})?\.smartmember\.(?:com|in|dev|soy|pro|co)(?:\/(?:.*)?)?$/i', $domain, $matches );

		if( !empty( $matches ) )
			return $matches;
		else
			return false;
	}

    public static function apiPath($route)
    {
        return \Config::get('app.url') . $route;
    }

    public static function getSubdomain()
    {
        if (Request::header("subdomain")) {
            return Request::header("subdomain");
        }

        if (\Input::has("subdomain")) {
            return \Input::get("subdomain");
        }

        if (Request::header('origin')) {
            $domain = Request::header("origin");
        } else if (isset($_SERVER["HTTP_REFERER"])) {
            $domain = $_SERVER["HTTP_REFERER"];
        }

        //Check for domain mapping

        if (!empty($_SERVER["HTTP_REFERER"]) && self::isCustomDomain( $_SERVER["HTTP_REFERER"] ) ) {
            $domain = explode("//", $_SERVER["HTTP_REFERER"]);
            $domain = explode("/", $domain[1]);
            $domain = array_shift($domain);
            $site = Site::whereDomain($domain)->first();
            if( $site )
            	return $site->subdomain;
        }

        if (isset($domain)) {
            $explode = explode(".", $domain);
            $subdomain = explode("//", $explode[0]);
			if( !empty( $subdomain[1] ) )
            	return $subdomain[1];
        }
    }

	public static function getDomain()
	{
		//Check for domain mapping

		if (!empty($_SERVER["HTTP_REFERER"]) && self::isCustomDomain( $_SERVER["HTTP_REFERER"] ) ) {
			$domain = explode("//", $_SERVER["HTTP_REFERER"]);
			$domain = explode("/", $domain[1]);
			$domain = array_shift($domain);
			$site = Site::whereDomain($domain)->first();
			if( $site )
				return $site->domain;
		}

		return false;
	}

    public static function getRealIP()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarder = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $forwarder = getenv('HTTP_X_FORWARDED_FOR');
        }
        if ($forwarder != '') {
            $client_ip =
                (!empty($_SERVER['REMOTE_ADDR'])) ?
                    $_SERVER['REMOTE_ADDR']
                    :
                    ((!empty($_ENV['REMOTE_ADDR'])) ?
                        $_ENV['REMOTE_ADDR']
                        :
                        "unknown");

            // Proxies are added at the end of this header
            // Ip addresses that are "hiding". To locate the actual IP
            // User begins to look for the beginning to find
            // Ip address range that is not private. If not
            // Found none is taken as the value REMOTE_ADDR

            $entries = preg_split('[, ]', $forwarder);

            reset($entries);
            while (list(, $entry) = each($entries)) {
                $entry = trim($entry);
                if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)) {
                    // http://www.faqs.org/rfcs/rfc1918.html
                    $private_ip = array(
                        '/^0\./',
                        '/^127\.0\.0\.1/',
                        '/^192\.168\..*/',
                        '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                        '/^10\..*/');

                    $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

                    if ($client_ip != $found_ip) {
                        $client_ip = $found_ip;
                        break;
                    }
                }
            }
        } else {
            $client_ip =
                (!empty($_SERVER['REMOTE_ADDR'])) ?
                    $_SERVER['REMOTE_ADDR']
                    :
                    ((!empty($_ENV['REMOTE_ADDR'])) ?
                        $_ENV['REMOTE_ADDR']
                        :
                        "unknown");
        }

        return $client_ip;
    }

    public static function getSite()
    {
        $subdomain = self::getSubdomain();
        if ($subdomain)
		{
            $site = Site::whereSubdomain($subdomain)->first();
			if( $site )
            	return $site;
        }

		if (!empty($_SERVER["HTTP_REFERER"]) && self::isCustomDomain( $_SERVER["HTTP_REFERER"] ) )
		{
			$domain = explode( "//", $_SERVER[ "HTTP_REFERER" ] );
			$domain = explode( "/", $domain[ 1 ] );
			$domain = array_shift( $domain );
			$site   = Site::whereDomain( $domain )->first();

			if( $site )
				return $site;
		}

        return false;
    }

    public static function replaceSubdomain($replace_string = "app")
    {
        $url = Request::header("origin");
        $explode = explode(".", $url);
        $subdomain = explode("//", $explode[0]);
        $replacer = $subdomain[1];
        return str_replace($replacer, $replace_string, $url);
    }

    public static function getOrigin($referrer = false)
    {
        if ($referrer)
            return Request::header("referer");

        return Request::header("origin");
    }

    public static function getPageIdByUrl($url = '')
    {
        $explode = explode(".", $url);
        $subdomain = explode("//", $explode[0]);
        if (!isset($subdomain[1]))
            return '';

        $site = Site::whereSubdomain($subdomain[1])->first();
        if ($site)
            return $site->id;

        return '';
    }

    public static function getPageIdBySubdomain($subdomain = '')
    {
        if (!$subdomain)
            $subdomain = self::getSubdomain();

        $site = Site::whereSubdomain($subdomain)->first();
        if ($site)
            return $site->id;

        return '';
    }
}