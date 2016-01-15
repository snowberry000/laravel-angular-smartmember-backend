<?php namespace App\Models\BridgePage;

use App\Models\Root;
use App\Models\SiteMetaData;

class SwapSpot extends Root
{
    protected $table = 'swapspots';

    public static function savePage($swapspots, $site_id, $bridge_page_id)
    {
        if ($swapspots) {
            unset($swapspots['emailListId']);
            unset($swapspots['email_options']);
            unset($swapspots['name_options']);
            if (isset($swapspots['turn_optin_to_member']))
            {
                $pageMetaData = SiteMetaData::whereSiteId($site_id)->whereKey('turn_optin_to_member')->first();

                if (!$pageMetaData) {
                    $pageMetaData = new SiteMetaData();
                    $pageMetaData->site_id = $site_id;
                    $pageMetaData->key = 'turn_optin_to_member';
                }

                $pageMetaData->value = $swapspots['turn_optin_to_member'];

                $pageMetaData->save();
            }
            //$key here is the name of each swapspot
            foreach ($swapspots as $key => $value) {
                $swapspot = SwapSpot::whereSiteId($site_id)->whereBridgePageId($bridge_page_id)->whereName($key)->first();
                if (!$swapspot) {
                    $swapspot = new SwapSpot();
                }
                $swapspot->site_id = $site_id;
                $swapspot->name = $key;
                $swapspot->value = $value;
                $swapspot->bridge_page_id = $bridge_page_id;
                $swapspot->save();
            }
        }
    }
}