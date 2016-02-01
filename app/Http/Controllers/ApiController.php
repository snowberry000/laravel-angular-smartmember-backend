<?php namespace App\Http\Controllers;

use DB;
use Input;
use App;

class ApiController extends Controller
{

    /*
           A model associated with a Resource
    */
    protected $model;

    public function __construct(){
        $this->middleware('auth');
    }

    /*
        Getting a list of resources
    */
    public function index(){
        $page_size = config("vars.default_page_size");
        $query = $this->model;

		if( !Input::has('bypass_paging') || !Input::get('bypass_paging') )
        	$query = $query->take($page_size);

        $query = $query->orderBy('id' , 'DESC');
        $query = $query->whereNull('deleted_at');
        foreach (Input::all() as $key => $value){
            switch($key){
                case 'q':
                    if (Input::get('q')){
                        $query = $this->model->applySearchQuery($query,$value);
                    }
                    break;
                case 'p':
                    $query->skip((Input::get('p')-1)*$page_size);
                    break;
				case 'bypass_paging':
					break;
                case "ignore":
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }
        return $query->get();
    }

	public function paginateIndex($params = []){
		$page_size = config("vars.default_page_size");
		$query = $this->model;

		$query = $query->orderBy('id' , 'DESC');
		$query = $query->whereNull('deleted_at');
		foreach (Input::all() as $key => $value){
			switch($key){
				case 'q':
					if (Input::get('q')){
                        $query = $this->model->applySearchQuery($query,$value);
                    }
					break;
				case 'view':
				case 'p':
				case 'bypass_paging':
					break;
				default:
					if( !empty( $value ) )
						$query->where($key,'=',$value);
			}
		}

		$return = [];

        if(isset($params['distinct']) && $params['distinct'])
		  $return['total_count'] = $query->distinct()->count('user_id');
        else
            $return['total_count'] = $query->count();
		if( !Input::has('bypass_paging') || !Input::get('bypass_paging') )
			$query = $query->take($page_size);

		if( Input::has('p') )
			$query->skip((Input::get('p')-1)*$page_size);

		$return['items'] = $query->get();

		return $return;
	}

    /*
        Get a single resource
    */
    public function show($model){
        return $model;
    }

    /*
        Store a resource
    */
    public function store(){
        $record = $this->model->create(Input::except(['access_token', 'token' , 'send_email']));
        
        if (!$record->id){
            App::abort(401, "The operation requested couldn't be completed");
        }
        return $record;
    }

    /*
        Update a resource
    */
    public function update($model){
        $model->fill(Input::except('_method' , 'send_email'));
        $model->save();
        return $model;
    }

    /*
        Delete a resource
    */
    public function destroy($model){        
        $response = array('success' => false);
        $response["success"] = $model->delete();
        return $response;
    }

}