<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\AffiliateLeaderboardController;
use App\Models\AffiliateContest;
use Input;

class AffiliateContestController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new AffiliateContest();
        $this->middleware('auth',['except'=>['getByPermalink','show']]); 
        $this->middleware('admin',['except'=>['getByPermalink']]); 
    }

    public function getByPermalink($id){
        $affcontest = AffiliateContest::wherePermalink($id)->whereSiteId( $this->site->id )->first();
        if($affcontest){
			return $this->show( $affcontest );
        }
        \App::abort('404','Lesson not found');
    }

    public function index()
    {
        //$current_company_id = Company::getOrSetCurrentCompany();

        //if ($current_company_id)
        //{
			$page_size = config("vars.default_page_size");
			//$query = $this->model->whereCompanyId( $current_company_id );
            $query = $this->model->whereSiteId($this->site->id);
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
        /*}
        else 
        {
            App::abort(408, "You must be signed in to a team to see the affiliate contest lists.");
        }*/
        
    }
    
    public function store()
    {
        //$current_company_id = Company::getOrSetCurrentCompany();

        //if ($current_company_id)
        //{
            //\Input::merge(array('company_id' => $current_company_id));
             \Input::merge(array('site_id' => $this->site->id));
             $record = $this->model->create(\Input::except('token'));
            
            if (!$record->id){
                App::abort(401, "The operation requested couldn't be completed");
            }
            return $record;   
        //}
        //else
        //{
       //     App::abort(408, "You must be signed in to a team to create a contest.");
        //}
       
    }

    public function update($model)
    {
        //$current_company_id = Company::getOrSetCurrentCompany();

        //if ($current_company_id)
        //{
		    $model->fill(\Input::except('sites', 'company_id'));

            $model->save();

            return $model;      
        //}
        //else
        //{
        //     App::abort(408, "You must be signed in to a team to create a contest.");
        //}
       
    }
}