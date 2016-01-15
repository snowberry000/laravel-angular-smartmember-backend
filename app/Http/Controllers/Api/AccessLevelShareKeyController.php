<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\AccessLevel;
use App\Models\AccessLevelShareKey;
use Input;
use Auth;


class AccessLevelShareKeyController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new AccessLevelShareKey;
    }

    public function index()
    {
        //return $this->model->with(['open'])->whereCompanyId($current_company_id)->get();
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->with('destination_site');
        $query = $query->whereNull('deleted_at');
        $query = $query->where('originate_site_id', $this->site->id);
        foreach (Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
				case 'view':
				case 'bypass_paging':
                case 'p':
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }

		$return = [];

		$return['total_count'] = $query->count();

		if( !\Input::has('bypass_paging') || !\Input::get('bypass_paging') )
			$query = $query->take($page_size);

		if( \Input::has('p') )
			$query = $query->skip((\Input::get('p')-1)*$page_size);

        $keys = $query->get();

		$return['items'] = $keys;

		return $return;
    }

    public function generateShareKey()
    {
        $access_level_id = \Input::get('access_level_id');
        $key = md5($access_level_id.microtime().rand().$this->site->id);

        $new_key = AccessLevelShareKey::create(['key' => $key, 'originate_site_id' => $this->site->id,
                                        'destination_site_id' => '', 'access_level_id' => $access_level_id]);

        return $new_key;
    }

    public function getAssociatedKey()
    {
        //return $this->model->with(['open'])->whereCompanyId($current_company_id)->get();
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->with('originate_site');
        $query = $query->whereNull('deleted_at');
        $query = $query->where('destination_site_id', $this->site->id);
        foreach (Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
                case 'view':
                case 'bypass_paging':
                case 'p':
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }

        $return = [];

        $return['total_count'] = $query->count();

        if( !\Input::has('bypass_paging') || !\Input::get('bypass_paging') )
            $query = $query->take($page_size);

        if( \Input::has('p') )
            $query = $query->skip((\Input::get('p')-1)*$page_size);

        $keys = $query->get();

        $return['items'] = $keys;

        return $return;
    }

    public function getGrantedShareAccessLevels()
    {
        $access_levels = AccessLevelShareKey::with('access_level')->whereDestinationSiteId($this->site->id)->get();
        return $access_levels;
    }

    public function store()
    {
        if (!\Input::has('key'))
            \App::abort(403, 'You need to have a key to use this feature');

        $existing_key = AccessLevelShareKey::whereKey(\Input::get('key'))->first();
        if (isset($existing_key))
        {
            if (empty($existing_key->destination_site_id))
            {
                $existing_key->destination_site_id = $this->site->id;
                $existing_key->save();

                return $existing_key;
            } else
                \App::abort(403, 'This key has been associated with a site. Please obtain a new key');
        } else
            \App::abort(403, 'This key does not exist');
    }
}
