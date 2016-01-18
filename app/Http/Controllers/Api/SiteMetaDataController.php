<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\SiteMetaData;
use App\Models\Site;
use App\Models\BridgePage;
use App\Models\Permalink;
use Input;
use PRedis;

class SiteMetaDataController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new SiteMetaData();
        $this->middleware('auth' , ['except'=>array('getTrackingCode')]);
        $this->middleware('admin',['except'=>array('index','show','getTrackingCode')]);
    }

    public function index()
    {
        $site_id = $this->site->id;
        Input::merge(array('site_id' => $site_id));
        $this->model->with("menu_items");
        $meta_data = parent::index();
        return $meta_data;
    }

    public function save()
    {
        if (Input::has('site')){
            $site = Input::get('site');
            $this->site->fill($site);
            $this->site->save();
        }

        $is_bridgepage = false;
        $bpage_permalink = "";
        //TODO: check if admin
        $site_id = $this->site->id;
        foreach (Input::except(['site','import_queue_locked']) as $key => $input) {
            $pageMetaData = SiteMetaData::whereSiteId($site_id)->whereKey($key)->first();
            if (!$pageMetaData) {
                $pageMetaData = new SiteMetaData();
                $pageMetaData->site_id = $site_id;
                $pageMetaData->key = $key;
            }
            $pageMetaData->value = $input;
            $pageMetaData->save();

            if ($pageMetaData->key == 'homepage_url')
            {
                $bridgepage = Permalink::wherePermalink($pageMetaData->value)->whereSiteId($this->site->id)
                    ->whereType('bridge_bpages')->first();
                if (!$bridgepage)
                {
                    $bridgepage_recheck = BridgePage::wherePermalink($pageMetaData->value)->whereSiteId
                    ($this->site->id)->first();
                    if (isset($bridgepage_recheck))
                    {
                        $is_bridgepage = true;
                        $bpage_permalink = $pageMetaData->value;
                    }
                } else {
                    $is_bridgepage = true;
                    $bpage_permalink = $pageMetaData->value;
                }
            }
        }

        if ($is_bridgepage)
        {
            $bpageKey = $this->site->subdomain . ':homepage' . ':*';
            $keys = PRedis::keys($bpageKey);
            foreach ($keys as $key)
            {
                \Log::info("Deleting " . $key);
                PRedis::del($key);
            }
            $bpageKey = $this->site->subdomain . ':' . 'homepage' . ':type';
            PRedis::setex($bpageKey, 24 * 60 * 60, 'bridge_bpages');
            $bpageKey = $this->site->subdomain . ':' . 'homepage' . ':permalink';
            PRedis::setex($bpageKey, 24 * 60 * 60, $bpage_permalink);
            if( !empty( $this->site->domain ) )
            {
                $key = $this->site->domain . ':' . 'homepage' . ':type';
                PRedis::setex($key, 24 * 60 * 60, 'bridge_bpages');

                $key = $this->site->domain . ':' . 'homepage' . ':subdomain';
                PRedis::setex($key, 24 * 60 * 60, $this->site->subdomain);

                $key = $this->site->domain . ':' . 'homepage' . ':permalink';
                PRedis::setex($key, 24 * 60 * 60, $bpage_permalink);
            }
        } else {
            $bpageKey = $this->site->subdomain . ':homepage' . ':type';
            PRedis::del($bpageKey);
            $bpageKey = $this->site->subdomain . ':homepage' . ':permalink';
            PRedis::del($bpageKey);
            if (!empty($this->site->domain))
            {
                $bpageKey = $this->site->domain . ':homepage' . ':type';
                PRedis::del($bpageKey);
                $bpageKey = $this->site->domain . ':homepage' . ':permalink';
                PRedis::del($bpageKey);
                $bpageKey = $this->site->domain . ':homepage' . ':subdomain';
                PRedis::del($bpageKey);
            }
        }
    }

    public function saveItem( $key, $value )
    {
        $site_id = $this->site->id;

        $pageMetaData = SiteMetaData::whereSiteId($site_id)->whereKey($key)->first();

        if (!$pageMetaData) {
            $pageMetaData = new SiteMetaData();
            $pageMetaData->site_id = $site_id;
            $pageMetaData->key = $key;
        }
        $pageMetaData->value = $value;

        $no_script_areas = array(
            'google_analytics_id',
            'facebook_retargetting_pixel',
            'facebook_conversion_pixel',
            'bing_id',
            'bing_webmaster_tag',
            'google_webmaster_tag'
        );

        if( in_array( $pageMetaData->key,$no_script_areas ) )
            $pageMetaData->value = strip_tags( $pageMetaData->value );

        $pageMetaData->save();
    }

    public function getItem( $key )
    {
        $site_id = $this->site->id;

        $pageMetaData = SiteMetaData::whereSiteId($site_id)->whereKey($key)->first();

        if ($pageMetaData)
            return $pageMetaData->value;
        else
            return 0;
    }

    public function saveSingleOption()
    {
		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

        $site_id = $this->site->id;
        foreach (Input::get() as $key => $value) {
            $pageMetaData = SiteMetaData::whereSiteId($site_id)->whereKey($key)->first();
            if (!$pageMetaData) {
                $pageMetaData = new SiteMetaData();
                $pageMetaData->site_id = $site_id;
                $pageMetaData->key = $key;
            }
            $pageMetaData->value = $value;
            $pageMetaData->save();
        }

    }

    public function getOptions()
    {
		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

        $pageMetaData = SiteMetaData::whereSiteId($this->site->id)->whereIn("key",Input::get())->get();
        return $pageMetaData;
    }

    public function getTrackingCode()
    {
        if (isset($this->site->id))
        {
            $data = SiteMetaData::whereSiteId($this->site->id)->whereIn("key",array('google_analytics_id','facebook_retargetting_pixel','facebook_conversion_pixel','bing_id','google_webmaster_tag','bing_webmaster_tag'))->get();
            $tracking_code = array();
            if( !empty( $data ) )
            {
                foreach( $data as $key => $val )
                {
                    if( !empty( $val[ 'key' ] ) && $val[ 'key' ] == 'google_analytics_id' && !empty( $val[ 'value' ] ) )
                        $tracking_code[ 'google_analytics_id' ] = $val[ 'value' ];
                    elseif( !empty( $val[ 'key' ] ) && $val[ 'key' ] == 'facebook_retargetting_pixel' && !empty( $val[ 'value' ] ) )
                        $tracking_code[ 'facebook_retargetting_pixel' ] = $val[ 'value' ];
                    elseif( !empty( $val[ 'key' ] ) && $val[ 'key' ] == 'facebook_conversion_pixel' && !empty( $val[ 'value' ] ) )
                        $tracking_code[ 'facebook_conversion_pixel' ] = $val[ 'value' ];
                    elseif( !empty( $val[ 'key' ] ) && $val[ 'key' ] == 'bing_id' && !empty( $val[ 'value' ] ) )
                        $tracking_code[ 'bing_id' ] = $val[ 'value' ];
                    elseif( !empty( $val[ 'key' ] ) && $val[ 'key' ] == 'google_webmaster_tag' && !empty( $val[ 'value' ] ) )
                        $tracking_code[ 'google_webmaster_tag' ] = $val[ 'value' ];
                    elseif( !empty( $val[ 'key' ] ) && $val[ 'key' ] == 'bing_webmaster_tag' && !empty( $val[ 'value' ] ) )
                        $tracking_code[ 'bing_webmaster_tag' ] = $val[ 'value' ];
                }
            }

            if (\Input::has('permalink'))
            {
                $permalink = \Input::get('permalink');
                if ($permalink != '')
                {
                    $permalink_rec = Permalink::whereSiteId($this->site->id)->wherePermalink($permalink)->first();

                    if (isset($permalink_rec->id) && $permalink_rec->type == "bridge_bpages")
                    {
                        $meta = BridgePage::getMeta($permalink_rec->permalink,6,$this->site);
                        if ($meta)
                        {
                            $tracking_code[ 'facebook_retargetting_pixel' ] = !empty( $meta[ 'fb_retargeting_pixel_id' ] ) ? $meta[ 'fb_retargeting_pixel_id' ] : '';
                            $tracking_code[ 'fb_conversion_tracking_pixel_id' ] = !empty( $meta[ 'fb_conversion_tracking_pixel_id' ] ) ? $meta[ 'fb_conversion_tracking_pixel_id' ] : '';
                        }
                    }
                }
            }

            return $tracking_code;
        }



    }

    public function wizard()
    {
        $site_id = $this->site->id;
        $key = 'show_wizard';
        $pageMetaData = SiteMetaData::whereSiteId($site_id)->whereKey($key)->first();
        if(!$pageMetaData){
            $pageMetaData = new SiteMetaData();
            $pageMetaData->site_id = $site_id;
            $pageMetaData->key = $key;
        }
        $pageMetaData->value = \Input::get('value');
        $pageMetaData->save();
    }
}
