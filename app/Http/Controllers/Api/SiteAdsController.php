<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\SiteAds;
use App\Models\Site;
use Input;

class SiteAdsController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new SiteAds();
        $this->middleware('auth',['except'=>array('postTrackClicks', 'postTrackViews')]); 
        $this->middleware('admin',['except'=>array('index','show', 'postTrackClicks', 'postTrackViews')]); 
    }

    public function index(){
        \Input::merge(array('site_id' => $this->site->id));
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->take($page_size);
        $query = $query->orderBy('sort_order' , 'ASC');
        $query = $query->orderBy('created_at' , 'DESC');
        $query = $query->whereNull('deleted_at');
        $query = $query->where(function($q){
			$q->whereNull('custom_ad');
			$q->orwhere('custom_ad','');
		});
        foreach (Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
				case 'custom_ad':
					if( !$value || $value == 'false' )
						$query = $query->where(function($q){
							$q->whereNull('custom_ad');
							$q->orwhere('custom_ad','');
						});
                case 'p':
                    $query->skip((Input::get('p')-1)*$page_size);
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }
        return $query->get();
    }

    public function postTrackClicks($id)
    {
    	$ad = SiteAds::find($id);
    	if ( empty($ad) ) return;

    	$ad->clicks = $ad->clicks + 1;
    	$ad->save();
    }

    public function putAds()
    {
        $input=\Input::get("adds");
        //dd($input);
        foreach ($input as $key => $value) {
            SiteAds::whereId($value['id'])->
            update(['sort_order' => $value['sort_order'],'display' => $value['display']]);
        }
    }

    public function postTrackViews($id = null)
    {
        if ($this->site)
        {
            if (empty($id))
            {
                $ad = SiteAds::whereSiteId($this->site->id)->first();
                if ($ad)
                {
                    $ad->views = $ad->views + 1;
                    $ad->save();
                }
            } else {
                $ad = SiteAds::find($id);
                if ( empty($ad) ) return;
                $ad->views = $ad->views + 1;
                $ad->save();
            }
        }
    }
   
}