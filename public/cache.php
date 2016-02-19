<?php
require_once __DIR__.'/../vendor/predis/predis/autoload.php';

function getSubdomain(){
	$headers = getallheaders();

	if (isset($headers['subdomain'])) {
        return $headers['subdomain'];
    }

    if (isset($_REQUEST['subdomain'])) {
        return $_REQUEST['subdomain'];
    }

    if (isset($headers['origin'])) {
        $domain = $headers['origin'];
    } else if (isset($_SERVER["HTTP_REFERER"])) {
        $domain = $_SERVER["HTTP_REFERER"];
    }

    if (isset($domain)) {
        $explode = explode(".", $domain);
        $subdomain = explode("//", $explode[0]);
		if( !empty( $subdomain[1] ) )
        	return $subdomain[1];
    }
}

function getFormattedRequest(){
    $request_uri = $_SERVER["REQUEST_URI"];
    $request_uri = explode("?", $request_uri);
    $request_uri = str_replace("/", "_", array_shift($request_uri)) ;

    return $request_uri;
}

function getDomain(){
	$headers = getallheaders();

	$domain = false;

	if (isset($headers['origin'])) {
		$domain = $headers['origin'];
	} else if (isset($_SERVER["HTTP_REFERER"])) {
		$domain = $_SERVER["HTTP_REFERER"];
	}

	if( $domain )
	{
		$domain_bits = explode( '//', $domain );

		if( !empty( $domain_bits[1] ) )
			$domain = str_replace( '/', '', $domain_bits[1] );
	}

	return $domain;
}

function getKey(){
	$domain = getDomain();
	$subdomain = getSubdomain();
	$require_uri = getFormattedRequest();
	$headers = getallheaders();

	$key = $subdomain . ":" . $require_uri;
	if (isset($headers["Authorization"])){
        $authorization = explode(" ",$headers["Authorization"]);
        list($type,$access_token) = $authorization;

        $key .= ":" . $access_token;
    }

    return $key;
}

function getDomainKey(){
	$domain = getDomain();
	$require_uri = getFormattedRequest();
	$headers = getallheaders();

	$key = $domain . ":" . $require_uri;
	if (isset($headers["Authorization"])){
		$authorization = explode(" ",$headers["Authorization"]);
		list($type,$access_token) = $authorization;

		$key .= ":" . $access_token;
	}

	return $key;
}


if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $client = new Predis\Client();
    $data = $client->get(getDomainKey());

	if( !$data )
    	$data = $client->get(getKey());

    if ($data){
        header('Content-Type: application/json');
        echo $data;
        exit;
    }
}
