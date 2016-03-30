<?php

namespace App\Helpers;

class CurlHelper{

    public static function get($url, $query_params,$json_format = true){
    
        $req = curl_init($url);

        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_POST, true );
        curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($query_params));
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2 );
        //curl_setopt($req, CURLOPT_VERBOSE, true);

        // TODO: Additional error handling

        if (!$json_format){
            return curl_exec($req);
        }

        $resp = json_decode(curl_exec($req), true);

        $respCode = curl_getinfo($req);

        \Log::info($query_params);

        curl_close($req);
        return $resp;
    }

    public static function getWithHeaders($url, $query_params, $headers = array(), $json_format = true){

        $req = curl_init($url);

        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_POST, true );
        curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($query_params));
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2 );
        //curl_setopt($req, CURLOPT_VERBOSE, true);

        curl_setopt($req, CURLOPT_HTTPHEADER, $headers);

        // TODO: Additional error handling

        if (!$json_format){
            return curl_exec($req);
        }

        $resp = json_decode(curl_exec($req), true);

        $respCode = curl_getinfo($req);

        \Log::info($query_params);

        curl_close($req);
        return $resp;
    }

    public static function post($url, $query_params , $headers, $json_payload = false){
    
        $req = curl_init($url);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_POST, true );
        if (!$json_payload)
            curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($query_params));
        else
            curl_setopt($req, CURLOPT_POSTFIELDS, json_encode($query_params));

        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt($req, CURLINFO_HEADER_OUT, true);
        curl_setopt($req, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:27.0) Gecko/20100101 Firefox/27.0');

        /* Tell cURL NOT to return the headers */
        curl_setopt($req, CURLOPT_HTTPHEADER, $headers);

        //curl_setopt($req, CURLOPT_VERBOSE, true);

        // TODO: Additional error handling

        //dd($headers);
        $resp = json_decode(curl_exec($req), true);
        $respCode = curl_getinfo($req);
        //\Log::info("Posting to webinar" . json_encode($respCode));
        curl_close($req);
        return $resp;

    }
    public static function delete($url, $query_params,$json_format=true){
        $req = curl_init($url);

        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_POST, true );
        curl_setopt($req, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($query_params));
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2 );
        return curl_exec($req);
    }

    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


    public static function actual_get($url , $query_params, $headers = array()) {
            $header = '';
            $body = '';
            /* Update URL to container Query String of Paramaters */
            $url .= '?' . http_build_query($query_params);

            /* cURL Resource */
            $ch = curl_init();

            /* Set URL */
            curl_setopt($ch, CURLOPT_URL, $url);

            /* Tell cURL to return the output */
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            /* Tell cURL NOT to return the headers */
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2 );
            \Log::info($headers);
            $data = curl_exec($ch);

            if (strpos($url, 'constantcontact') !== FALSE)
            {
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $header = substr($data, 0, $header_size);
                $body = substr($data, $header_size);
                $data = $body;
            }
            curl_close($ch);

            return json_decode($data, true);
            /* Execute cURL, Return Data */

            /*$info = curl_getinfo($ch);

            \Log::info($info);

            /* Check HTTP Code */
            //$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            //\Log::info($status);

            /* Close cURL Resource */
            //curl_close($ch);
    }
}