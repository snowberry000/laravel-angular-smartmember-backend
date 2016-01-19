<?php namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Api\SMController;
use App\Models\Site\Role;
use App\Models\Site\CustomRole;
use App\Models\Site;
use App\Models\User;
use App\Models\ImportQueue;

use SMRole;
use Input;

class RoleController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Role();
    }

    public function getSupportAgent()
    {
        $agents = Role::getFullMembersWithCapability($this->site->id, 'manage_support_tickets');
        return array('items' => $agents, 'total_count' => count($agents));
    }

    public function index(){
    	\Input::merge(['site_id'=>$this->site->id]);
    	$this->model = Role::with(['user','accessLevel']);

        if (Input::get('q')){
            $users = User::where('first_name','like','%' . Input::get('q') . "%")->orWhere('last_name','like','%' . Input::get('q') . "%")->orWhere('email','like','%' . Input::get('q') . "%")->select(array('id'))->get();
            $this->model->whereIn('user_id' , $users);
            Input::merge(['q'=>null]);
        }

        return parent::paginateIndex();
    }

    public function passes(){
        \Input::merge(['site_id'=>$this->site->id]);
        $this->model = Role::with(['user','accessLevel'])->whereNotNull('access_level_id');

        if (Input::get('q')){
            $users = User::where('first_name','like','%' . Input::get('q') . "%")->orWhere('last_name','like','%' . Input::get('q') . "%")->orWhere('email','like','%' . Input::get('q') . "%")->select(array('id'))->get();
            $this->model->whereIn('user_id' , $users);
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

            $bits = preg_split('/[\n]+/', $emails );
            
            if( $bits )
            {
                $import_count = 0;

                foreach( $bits as $key => $value )
                {
                    $value = trim( $value );
                    if( !$value )
                        continue;
                    if (strpos($value,",") !== FALSE)
                    {
                        $line_parts = explode(",", $value);
                        $line = array();
                        $line['name'] = $line_parts[0];
                        $line['email'] = $line_parts[1];
                    } else {
                        $line = array();
                        $line['name'] = '';
                        $line['email'] = $value;
                    }

                    $users[] = $line;
                }
            }   
        }

        $access_levels = [];
        if (\Input::has('access_levels'))
            $access_levels = \Input::get('access_levels');

        $expiry = \Input::has('expired_at') ? \Input::get('expired_at') : '0';

        //$count = User::importUsers($users, array_keys($access_levels), $expiry, $this->site);
        ImportQueue::enqueue($users, array_keys($access_levels), $expiry, $this->site);
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

    public function getCSV()
    {
        $site_id = $this->site->id;
        $query = $this->model->with(["user"]);
        $query = $query->orderBy('id' , 'DESC')->whereNull('deleted_at')->whereSiteId($site_id);
        $roles = $query->get();

        $arrayCSV = array();

        foreach ($roles as $role) 
        {
            $arrayCSV [] = array($role->user['first_name']." ".$role->user['last_name'],$role->user['email'],$role['type'],$role['created_at']::parse()->format('d/m/Y'));
        }

        $this->outputCSV($arrayCSV);
    }

    

}