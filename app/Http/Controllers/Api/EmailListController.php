<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\EmailList;
use App\Models\Role;
use App\Models\Site;
use App\Models\EmailSubscriber;
use App\Models\AccessLevel;
use App\Models\SegmentTool;
use App\Models\EmailListLedger;
use Input;

class EmailListController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new EmailList();
    }

    public function index()
    {
        $page_size = config("vars.default_page_size");
        $user = \Auth::user();

        $query = $this->model;

		//$query = $query->take($page_size);

        $query = $query->orderBy('id' , 'DESC');
        $query = $query->whereNull('deleted_at');
        $query = $query->whereListType('user');
        $query = $query->whereAccountId(\Auth::user()->id);
        foreach (Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
                case 'p':                    
                    break;
                case 'bypass_paging':
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }
        $count = $query->count();
        if( !Input::has('bypass_paging') || !Input::get('bypass_paging') )
        {
            $query = $query->take($page_size);
        }
           
        if( Input::has('p') )
            $query = $query->skip((\Input::get('p')-1)*$page_size);

        $email_lists = $query->get();
        foreach ($email_lists as $list)
        {
            $list->total_subscribers = EmailListLedger::whereListId($list->id)->count();
        }
        return array('items' => $email_lists , 'total_count' => $count);
    }

    public function sendMailLists()
    {
        $page_size = config("vars.default_page_size");
        $user = \Auth::user();

        if ( empty( $user ) )
            App::abort(408, "You must be signed in to a team to see e-mail lists.");

        $query = $this->model;
        $query = $query->orderBy('id' , 'DESC');
        $query = $query->where('list_type','!=','segment');
        $query = $query->whereAccountId($user->id);
        foreach (Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
                case 'p':
                    $query = $query->skip((\Input::get('p')-1)*$page_size);
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }

        return $query->get();
    }

    public function show($model)
    {
    	return $model;
    }

    public function store()
    {
        if( empty( \Auth::user() ) )
            App::abort(408, "You must be signed in to a team to add an e-mail list.");

        \Input::merge(array('account_id'=> \Auth::user()->id ));

        return EmailList::createOrUpdate(\Input::all());
    }

    public function update($model)
    {
		if( empty( \Auth::user() ) )
			App::abort(408, "You must be signed in to a team to add an e-mail list.");

		\Input::merge(array('account_id'=> \Auth::user()->id ));

        return parent::update($model);
    }

    public function users()
    {
        $page = \Input::get('p', 1);

        if( empty( \Auth::user() ) )
            App::abort(408, "You must be signed in to a team to add an e-mail list.");

        $site_ids = Site::whereUserId( \Auth::user()->id )->select('id')->lists('id');

        $users = [];

        if( !empty( \Input::get('segment_query') ) )
        {
            $segment_query = \Input::get('segment_query');

            $segment = new SegmentTool($segment_query, $site_ids);

            if( $this->site->subdomain == 'sm' )
                $segment->setAdmin(TRUE);

            $users = $segment->getUsers($page);
        }

        return $users;
    }

}
