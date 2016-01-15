<?php

namespace App\Models;
use Carbon\Carbon;

class Link extends Root
{
    protected $table = "links";

    public function site() {
        return $this->belongsTo('App\Models\Site');
    }

    public function email() {
        return $this->belongsTo('App\Models\Email', 'email_id');
    }

    public function clicks() {
        return $this->hasMany('App\Models\Click', 'link_id');
    }

    public static function EncodeLinksInContent($content, $job_id)
    {
        $regex = '<[^>]+>(.*)<\/[^>]+>';
        preg_match_all("'<a\s+href=\"(.*)\"\s*>(.*)</a>'U", $content, $matches);
        if (!$matches[0])
            return $content;

        //$this->ItemDebug( $matches );
        $uniqueURL = $url = \Config::get('app.url') . '/trackClick?id=';

        $hash = '';

        foreach ($matches[2] as $k2 => $m2) {
            foreach ($matches[1] as $k1 => $m1) {
                $bits = explode('"', $m1);
                $bits = explode("'", $bits[0]);
                $matches[1][$k1] = $m1 = $bits[0];

                if (true || stristr($m1, $m2)) {
                    $hash = md5($matches[1][$k1] . '_' . $job_id);

                    Link::StoreLink($matches[1][$k1], $job_id, $hash);

                    $uniq = $uniqueURL . $hash; //."_".rand(1000,9999);
                    $matches[3][$k1] = $uniq . "&refLink=" . urlencode($m1);
                }
            }
        }

        if (!$matches[3])
            return $content;

        //$this->ItemDebug( $matches );
        //$this->ItemDebug( $content );
        // this is ugly...
        $fields = array();
        $fields[] = '"';
        $fields[] = "'";

        foreach ($fields as $prefix_key => $prefix_value) {
            foreach ($matches[3] as $key => $val) {
                $startAt = strpos($content, $prefix_value . $matches[1][$key]);

                if ($startAt !== false) {
                    $endAt = $startAt + 1 + strlen($matches[1][$key]);

                    $strBefore = substr($content, 0, $startAt + 1);
                    $strAfter = substr($content, $endAt);

                    $content = $strBefore . "@@@$key@@@" . $strAfter;
                }
            }
        }

        foreach ($matches[3] as $key => $val) {
            $content = str_replace("@@@$key@@@", $matches[3][$key], $content);
        }
        //$this->ItemDebug( $content );
        //exit;

        return $content;
    }

    public static function StoreLink($url, $job_id, $hash)
    {
        $existing_link = Link::whereHash($hash)->whereJobId($job_id)->first();
        if (!isset($existing_link))
        {
            $link = Link::insert(['url' => $url, 'job_id' => $job_id, 'hash' => $hash]);
            return $link;
        } else {
            return $existing_link;
        }
    }


}