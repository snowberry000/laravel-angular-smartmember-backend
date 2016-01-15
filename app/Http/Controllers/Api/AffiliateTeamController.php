<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\AffiliateTeam;
use App\Models\AffiliateTeamLedger;
use Input;

class AffiliateTeamController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new AffiliateTeam();
        $this->middleware('admin',['except'=>array('index','show')]); 
    }

    public function index()
    {
			$page_size = config("vars.default_page_size");
			$query = $this->model->with(['members'])->whereSiteId( $this->site->id );

			$query = $query->orderBy('id' , 'DESC');
			$query = $query->whereNull('deleted_at');
			foreach (Input::all() as $key => $value){
				switch($key){
					case 'q':
						$query = $this->model->applySearchQuery($query,$value);
						break;
					case 'view':
					case 'p':
					case 'bypass_paging':
						break;
					default:
						$query->where($key,'=',$value);
				}
			}

			$return = [];

			$return['total_count'] = $query->count();

			if( !Input::has('bypass_paging') || !Input::get('bypass_paging') )
				$query = $query->take($page_size);

			if( Input::has('p') )
				$query->skip((Input::get('p')-1)*$page_size);

			$return['items'] = $query->get();

			return $return;
    }

    public function store() 
    {
        //$current_company_id = Company::getOrSetCurrentCompany();

        //if ($current_company_id)
        //{
            //\Input::merge(array('company_id' => $current_company_id));
            \Input::merge(array('site_id' => $this->site->id));
            return parent::store();
        //}
        /*else
        {
            App::abort(408, "You must be signed in to a team to save an affiliate team.");
        }*/
    }

    public function show($model){
        return $this->model->with('members')->find($model->id);
    }

    public function update($model){
        return $model->update(\Input::all());
    }

}