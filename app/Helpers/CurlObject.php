<?php namespace App\Helpers;

class CurlObject {

    public function errno($ch) {
        return curl_errno($ch);
    }

    public function error($ch) {
        return curl_error($ch);
    }

    public function execute($ch) {
        return curl_exec($ch);
    }

    public function init($url) {
        return curl_init($url);
    }

    public function setopt ($ch , $option , $value) {
        return curl_setopt($ch, $option, $value);
    }

}