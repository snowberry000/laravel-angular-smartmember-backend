<?php

namespace App\Models;

class SeoSetting extends Root
{
    protected $table = "seo_settings";

    public function type(){
        return $this->belongsTo('App\Models\PermalinkType', "link_type");
    }

    public static function savePage($seo_settings, $site_id, $link_type, $target_id)
    {
        if ($seo_settings) {
            foreach ($seo_settings as $key => $value) {
                $seo = SeoSetting::whereSiteId($site_id)->whereLinkType($link_type)->whereTargetId($target_id)->whereMetaKey($key)->first();
                if (!$seo) {
                    $seo = new SeoSetting();
                }
                $seo->site_id = $site_id;
                $seo->link_type = $link_type;
                $seo->meta_key = $key;
                $seo->meta_value = $value;
                $seo->target_id = $target_id;
                $seo->save();
            }
        }
    }

}
