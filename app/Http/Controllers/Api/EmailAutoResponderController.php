<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\EmailList;
use App\Models\EmailAutoResponder;
use App\Models\Site\Role;
use Input;
use DB;

class EmailAutoResponderController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new EmailAutoResponder();
    }

    public function show($model){
        $this->model = $this->model->with(['emails', 'emailLists','accessLevels','sites']);
        return $this->model->where('id','=',$model->id)->whereSiteId( $this->site->id )->first();
    }

    public function index()
    {
        if( empty( $this->site->id ) )
            App::abort(408, "You must be signed in to a team to view autoresponders");

		$page_size = config("vars.default_page_size");
		$query = $this->model->with(['emails', 'emailLists','accessLevels','sites'])->whereSiteId( $this->site->id );

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
        if( empty( \Auth::user() ) )
            App::abort(408, "You must be signed in to add an autoresponder");

        \Input::merge(array('site_id'=>$this->site->id));

        return parent::store();
    }

    public function update($model){
        $data=\Input::except('token');
        $emails = [];
        if (array_key_exists('emails', $data))
        {
            $emails = $data['emails'];
            unset($data['emails']);
        }

        $lists = [];
        if (array_key_exists('post_lists', $data))
        {
            $lists = $data['post_lists'];
            unset($data['post_lists']);
        }
        $access_levels = [];
        if (array_key_exists('post_access_levels', $data))
        {
            $access_levels = $data['post_access_levels'];
            unset($data['post_access_levels']);
        }
        $sites = [];
        if (array_key_exists('post_sites', $data))
        {
            $sites = $data['post_sites'];
            unset($data['post_sites']);
        }
        $this->model->where('id',$model->id)->update($data);
        $model->emailLists()->sync($lists);
        $model->accessLevels()->sync($access_levels);
        $model->sites()->sync($sites);
        $tmp = [];
        foreach ($emails as $email)
        {
            unset($email['subject']);
            $tmp[] = $email;

        }
        $emails = $tmp;
        $emails_ids=array_pluck($emails, 'email_id');

		$model->emails()->sync($emails_ids);

        foreach ($emails as $key => $value) {
            DB::table('email_autoresponder_email')
            ->whereEmailId($value['email_id'])
            ->whereAutoresponderId($model->id)
            ->update(array('delay'=>$value['delay'],'unit'=>$value['unit'],'sort_order'=>$value['sort_order']));
        }
        
    }
}
