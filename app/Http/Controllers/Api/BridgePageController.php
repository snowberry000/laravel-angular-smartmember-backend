<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\BridgePage;
use App\Models\BridgePage\SwapSpot;
use App\Models\SmartLink;
use App\Models\Permalink;
use App\Models\BridgePage\BridgeTemplate;
use App\Models\BridgePage\BridgeType;
use App\Models\SiteMetaData;
use App\Http\Controllers\Api\SiteMetaDataController;
use App\Models\Site;
use PRedis;

class BridgePageController extends SMController
{

    public function __construct()
	{
		parent::__construct();
		$this->middleware( 'auth', [ 'except' => array( 'index', 'show', 'single', 'getByPermalink', 'checkHomepageBridgePage', 'initialLoadingData' ) ] );
		$this->middleware( 'access', [ 'only' => array( 'show', 'single' ) ] );
		$this->middleware( 'admin', [ 'except' => array( 'index', 'show', 'getByPermalink', 'checkHomepageBridgePage', 'initialLoadingData' ) ] );
		$this->model = new BridgePage();
	}

    public function index()
    {
	    // new BP doesn't get called from a site
	    if( $this->site )
            \Input::merge(array('site_id' => $this->site->id));

        $index = parent::paginateIndex();

		foreach( $index['items'] as $key => $item )
		{
			if( empty( $item->featured_image ) )
			{
				$swapspot = SwapSpot::whereBridgePageId( $item->id )->whereName('background_url')->first();

				if( $swapspot )
					$index['items'][ $key ]->featured_image = $swapspot->value;
			}
		}
        return $index;
    }

    public function getlist()
    {
        return $this->model->whereSiteId($this->site->id)->get();
    }

    public function show($model){
        $model = $this->model->with("seo_settings","swapspots")->whereId($model->id)->first();
        $model->template = BridgeTemplate::with('type')->whereId($model->template_id)->first();
        $model->template->preview_url  = 'templates/bptemplate/' . $model->template->type->folder_slug . '/' . $model->template->folder_slug . '/snippets.html';
        return $model;
    }

    public function single($id)
    {
        return $this->model->whereId($id)->first();
    }

	public function destroy($model)
	{
		$permalinks = Permalink::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->get();
		foreach( $permalinks as $permalink )
			$permalink->delete();

		return parent::destroy($model);
	}

    public function store()
    {
        // Todo here we can cache more bpage related stuff.
        $stored = parent::store();

        try
        {
            if (strlen($stored->permalink) > 0)
            {
                $key = $this->site->subdomain . ':' . $stored->permalink . ':type';
                PRedis::setex($key, 24 * 60 * 60, 'bridge_bpages');

				if( !empty( $this->site->domain ) )
				{
					$key = $this->site->domain . ':' . $stored->permalink . ':type';
					PRedis::setex($key, 24 * 60 * 60, 'bridge_bpages');
				}
            }
            
        }
        catch (Exception $e)
        {

        }

        return $stored;
    }

    public function update($model){
        
        // flush the keys for this bridge page.
        try
        {
            $bpageKey = $this->site->subdomain . ':' . $model->permalink . ':*';
            $keys = PRedis::keys($bpageKey);
            foreach ($keys as $key)
            {
                \Log::info("Deleting " . $key);
                PRedis::del($key);
            }
            $bpageKey = $this->site->subdomain . ':' . $model->permalink . ':type';
            PRedis::setex($bpageKey, 24 * 60 * 60, 'bridge_bpages');

			if( !empty( $this->site->domain ) )
			{
				$key = $this->site->domain . ':' . $model->permalink . ':type';
				PRedis::setex($key, 24 * 60 * 60, 'bridge_bpages');

				$key = $this->site->domain . ':' . $model->permalink . ':subdomain';
				PRedis::setex($key, 24 * 60 * 60, $this->site->subdomain);
			}
        }
        catch (Exception $e)
        {
            \Log::info($e->getMessage());
        }

        return $model->update(\Input::except('_method' , 'access', 'access_level_type'));
    }

    public function getByPermalink($id)
    {
        $page = BridgePage::wherePermalink($id)->whereSiteId($this->site->id)->first();
        if($page)
            return $this->show($page);
        \App::abort('404','Page not found');
    }

	public function initialLoadingData()
	{
		if( $this->site )
		{
			$possibilities = [
				'http://',
				'http:\/\/',
				'https://',
				'https:\/\/',
			];
			$domain = str_replace( $possibilities, '', $_SERVER['HTTP_REFERER'] );

			$domain_bits = explode( '/', $domain );

			if( count( $domain_bits ) > 1 && !empty( $domain_bits[1] ) )
			{
				$permalink = $domain_bits[ 1 ];

				$pos = strpos( $permalink, '?' );
				if( $pos !== false )
				{
					$permalink = substr( $permalink, 0, $pos );
				}
			}

			if( !empty( $permalink ) )
			{
				$page = BridgePage::wherePermalink( $permalink )->whereSiteId($this->site->id)->first();
				if($page)
					return $this->show($page);
				else
				{
					$smart_link = SmartLink::wherePermalink( $permalink )->whereSiteId($this->site->id)->first();

					if( $smart_link )
					{
						$redirect_url = SmartLink::getNextUrl( $smart_link->id );
						return [ 'type' => 'smart_link', 'redirect_url' => $redirect_url ];
					}
				}
			}
			else
			{
				$homepage_url_option = SiteMetaData::whereKey('homepage_url')->whereSiteId($this->site->id)->first();

				if( $homepage_url_option )
					$page = BridgePage::wherePermalink( $homepage_url_option->value )->whereSiteId($this->site->id)->first();
				else
					$page = false;

				if($page)
					return $this->show($page);
				else
					$page = false;
			}

			$site_meta_controller = new SiteMetaDataController();
			return [ 'type' => 'sm_data', 'data' => $site_meta_controller->getTrackingCode() ];
		}

		return;
	}

    public function checkHomepageBridgepage($domain)
    {
        if ( \Domain::isCustomDomain( $domain ) )
        {
            $site = Site::whereDomain($domain)->first();
            if ($site)
            {
                $homepage_url_option = SiteMetaData::whereKey('homepage_url')->whereSiteId($site->id)->first();
                if ($homepage_url_option)
                {
                    $bridgepage = Permalink::wherePermalink($homepage_url_option->value)->whereSiteId($site->id)
                        ->whereType('bridge_bpages')->first();
                    if (!$bridgepage)
                    {
                        $bridgepage_recheck = BridgePage::wherePermalink($homepage_url_option->value)->whereSiteId
                        ($site->id)->first();
                        if (isset($bridgepage_recheck))
                        {
                            return ['type' => 'bridge_bpages', 'homepage_url' => $homepage_url_option->value, 'subdomain' => $site->subdomain];
                        }
                    } else {
                        return ['type' => 'bridge_bpages', 'homepage_url' => $homepage_url_option->value, 'subdomain' => $site->subdomain];
                    }
                }
            }
        } else {
            if (isset($this->site->id))
            {
                $homepage_url_option = SiteMetaData::whereKey('homepage_url')->whereSiteId($this->site->id)->first();
                if ($homepage_url_option)
                {
                    $bridgepage = Permalink::wherePermalink($homepage_url_option->value)->whereSiteId($this->site->id)
                        ->whereType('bridge_bpages')->first();
                    if (!$bridgepage)
                    {
                        $bridgepage_recheck = BridgePage::wherePermalink($homepage_url_option->value)->whereSiteId
                        ($this->site->id)->first();
                        if (isset($bridgepage_recheck))
                        {
                            return ['type' => 'bridge_bpages', 'homepage_url' => $homepage_url_option->value, 'subdomain' => $this->site->subdomain];
                        }
                    } else {
                        return ['type' => 'bridge_bpages', 'homepage_url' => $homepage_url_option->value, 'subdomain' => $this->site->subdomain];
                    }
                }
            }
        }
    }
}