<?php namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Api\SMController;
use App\Models\Site\Role;
use App\Models\Site\CustomRole;
use App\Models\Site;
use App\Models\AccessLevel;
use App\Models\User;
use App\Models\ImportQueue;

use SMRole;
use Input;

class RoleController extends SMController
{
    public function __construct(){
        parent::__construct();

		$this->middleware( 'admin', ['only' => array( 'removeUserFromCurrentSite' ) ] );

        $this->model = new Role();
    }

    public function getSupportAgent()
    {
		if( \Input::has('site_id') && !empty( \Input::get('site_id') ) )
			$site_id = \Input::get('site_id');
		else
		{
			if( isset( $this->site ) && !empty( $this->site->id ) )
				$site_id = $this->site->id;
		}

		if( !empty( $site_id ) )
		{
			$site_ids = explode( ',', $site_id );
			$agents = Role::getFullMembersWithCapability( $site_ids, 'manage_support_tickets' );
			return array( 'items' => $agents, 'total_count' => count( $agents ) );
		}
		else
		{
			return array( 'items' => [ ['user' => \Auth::user() ]], 'total_count' => 0 );
		}
    }

    public function index(){
    	\Input::merge(['site_id'=>$this->site->id]);
    	$this->model = Role::with(['user','accessLevel']);

        if (Input::get('q')){
            $users = User::where('first_name','like','%' . Input::get('q') . "%")->orWhere('last_name','like','%' . Input::get('q') . "%")->orWhere('email','like','%' . Input::get('q') . "%")->select(array('id'))->get();
            $this->model->whereIn('user_id' , $users);
            Input::merge(['q'=>null]);
        }

        return parent::paginateIndex(array('distinct' => true));
    }

    public function passes(){
        \Input::merge(['site_id'=>$this->site->id]);
        $this->model = Role::with(['user','accessLevel'])->whereNotNull('access_level_id');

        if (Input::get('q')){
            $users = User::where('first_name','like','%' . Input::get('q') . "%")->orWhere('last_name','like','%' . Input::get('q') . "%")->orWhere('email','like','%' . Input::get('q') . "%")->select(array('id'))->get();
            $access_levels = AccessLevel::where('name' , 'like' , '%' . Input::get('q') . "%")->whereSiteId($this->site->id)->select(array('id'))->get();
            $this->model->where(function($q) use ($users , $access_levels){
                $q->whereIn('user_id' , $users)->orWhereIn('access_level_id' , $access_levels->lists('id'));
            });
            Input::merge(['q'=>null]);
        }

        return parent::paginateIndex();
    }

    public function outputCSV($data) {

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=members.csv');

        $output = fopen('php://output','w');

        foreach ($data as $datum){
            fputcsv($output, $datum);
        }
        fclose($output);
        exit;

    }

    public function postImport()
    {
        $users = [];
        if (\Input::has('emails'))
        {
            $emails = \Input::get('emails');

			$textarea = preg_replace( '/\s+/', ',', str_replace( array( "\r\n", "\r", "\n" ), ' ', trim( $emails ) ) );
			$bits = preg_split( "/[\r\n,]+/", $textarea, -1, PREG_SPLIT_NO_EMPTY );
            
            if( $bits )
            {
				$name = '';
                foreach( $bits as $key => $value )
                {
					$value = str_replace( ['"',"'"], '', trim( $value ) );

					if( !$value )
						continue;

					if( strpos( $value, '@' ) !== false )
					{
						$line = array();
						$line['name'] = $name;
						$line['email'] = $value;
						$name = '';

						$users[] = $line;
					}
					else
					{
						if( strlen( $name ) < 255 )
						{
							$name .= ' ' . $value;
							$name = substr( trim( $name ), 0, 255 );
						}
					}
                }
            }   
        }

        $access_levels = [];
        if (\Input::has('access_levels'))
            $access_levels = \Input::get('access_levels');

        $expiry = \Input::has('expired_at') ? \Input::get('expired_at') : '0';
        $email_welcome = \Input::has('email_welcome') ? \Input::get('email_welcome') : 0;
        $email_ac = \Input::has('email_ac') ? \Input::get('email_ac') : 0;

        //$count = User::importUsers($users, array_keys($access_levels), $expiry, $this->site);
        ImportQueue::enqueue($users, array_keys($access_levels), $expiry, $this->site, $email_welcome, $email_ac);
        //return $count;
    }

    public function removeUserFromSite(){
        $response = [];

        $roles = $this->model->where('type','!=','owner')->whereSiteId(\Input::get('site_id'))->whereUserId(\Input::get('user_id'))->whereNull('deleted_at')->get();
        foreach ($roles as $key => $value) {
            $response [] = $value;
            $value->delete();
        }
        return $response;
    }

	public function removeUserFromCurrentSite(){
		$response = [];

		$roles = $this->model->where('type','!=','owner')->whereSiteId( $this->site->id )->whereUserId(\Input::get('user_id'))->whereNull('deleted_at')->get();
		foreach ($roles as $key => $value) {
			$response [] = $value;
			$value->delete();
		}
		return $response;
	}

     public function getCSV()
    {
        $site_id = $this->site->id;
        $query = $this->model->with(["user","accessLevel"]);
        $query = $query->orderBy('id','desc')->whereNull('deleted_at')->whereSiteId($site_id);
        $roles = $query->get();
        //$roles = array_unique($roles );
        $arrayCSV = array();
        foreach ($roles as $role) {
            if(!empty($role->accessLevel['name']))
            {
                $arrayCSV [$role->user['first_name']." ".$role->user['last_name'].'!@~&'.$role->user['email'].'!@~&'.$role['type'].'!@~&'.'accessLevel'] [] =  $role->accessLevel['name'];
                // $arrayCSV [$role->user['first_name']." ".$role->user['last_name'].'!@~&'.$role->user['email'].'!@~&'.$role['type'].'!@~&'.'accessLevel'] [] =  $role->accessLevel['name'].','.$role['created_at']::parse()->format('d/m/Y');

                $arrayCSV [$role->user['first_name']." ".$role->user['last_name'].'!@~&'.$role->user['email'].'!@~&'.$role['type'].'!@~&'.'accessLevel'] = array_unique($arrayCSV [$role->user['first_name']." ".$role->user['last_name'].'!@~&'.$role->user['email'].'!@~&'.$role['type'].'!@~&'.'accessLevel']);
            }
        }
        $output = array(); 
        foreach ($arrayCSV as $key => $value) {
            $tempArr = explode("!@~&", $key);
            if (strlen(trim($tempArr[0].$tempArr[1]))<=0){
                continue;
            }
            $tempValue = rtrim(implode(',', $value), ',');
            $output [] = array($tempArr[0],$tempArr[1],$tempArr[2],$tempValue);   
        }
        $this->outputCSV($output);
    }
    
}