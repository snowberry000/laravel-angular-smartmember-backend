<?php namespace App\Models\AppConfiguration;

use App\Models\AppConfiguration;
use App\Models\Site;
use App\Models\Site\Role;
use App\Models\User;
use App\Models\Media;

use Curl;

class YouZign
{
    public function __construct()
    {
        $this->type = "youzign";
    }

    public static function importAssets($public_key, $token, $site_id)
    {
        $data = Curl::post('https://www.youzign.com/api/designs/', array(
            'key' => $public_key,
            'token' => $token,
        ), array());

        foreach ($data as $design)
        {
            if (isset($design['image_src']))
                Media::insert(['site_id' => $site_id, 'type' => 'youzign', 'source' => $design['image_src'][0]]);
            else
                return 'bad_credentials';
        }
    }
}

?>