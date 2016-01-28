<?php

namespace App\Models;
use Carbon\Carbon;

class Open extends Root
{
    protected $table = "opens";

    public static function AddPixelToContent($content, $segment_id)
    {
        $asset_url = \Config::get('app.url') . '/trackOpen';

        $content .= '<img style="display:none;" src="' . $asset_url . '?job_id=@@@job_id@@@&subscriber_id=@@@subscriber_id@@@&network_id=@@@network_id@@@&segment_id=@@@segment_id@@@&list_type=@@@list_type@@@" alt="" width="1" height="1"/>';

        return $content;
    }

}