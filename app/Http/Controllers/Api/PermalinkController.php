<?php namespace App\Http\Controllers\Api;

use App\Models\Permalink;
use App\Models\PermalinkStats;
use App\Models\SmartLink;
use Illuminate\Http\Request;
use Input;
use Exception;


class PermalinkController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Permalink();   

        $this->middleware("auth",['except' => array('getByPermalink')]);
        $this->middleware('access' , ['only'=>array('getByPermalink')]);
        $this->middleware('admin',['except'=>array('getByPermalink')]);
    }

    public function getByPermalink($permalink, Request $request){
        if (!$this->site){
            \App::abort(405,"This is not a valid URL. Please check your URL.");
        }

    	$permalink = Permalink::whereSiteId($this->site->id)->wherePermalink($permalink)->first();

        // Collect stats regarding a perma-link.
        try
        {
            $user_id = null;
            if (\App\Helpers\SMAuthenticate::set())
            {
                $user_id = \Auth::user()->id;
            }
            $data = [
                    'site_id' => $this->site->id,
                    'permalink_id' => $permalink->id,
                    'ip' => $request->ip(),
                    'user_id' => $user_id,
                ];
            PermalinkStats::create($data);
        }
        catch (Exception $e){}
        

    	if ($permalink){
			$permalink->subdomain = $this->site->subdomain;

			if( $permalink->type == 'smart_links' )
				$permalink->redirect_url = SmartLink::getNextUrl( $permalink->target_id );

    		return $permalink;
    	}

    	\App::abort(405,"This is not a valid URL. Please check your URL.");


    }
}