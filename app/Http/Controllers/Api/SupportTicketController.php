<?php namespace App\Http\Controllers\Api;

use App\Models\SupportTicket;
use App\Models\SupportTicketAction;
use App\Models\User;
use App\Models\TeamRole;
use App\Models\Site\Role;
use App\Models\Site;
use App\Models\LinkedAccount;
use App\Models\AccessLevel\Pass;
use App\Helpers\SMAuthenticate;
use App\Models\AppConfiguration\SendGridEmail;
use SendGrid;
use Input;
use DateTime;
use View;

class SupportTicketController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->middleware('auth' , ['except'=>array('store' ,'rate')]);
        $this->middleware('admin',['except'=>array('store' ,'index', 'update' , 'show' ,'userTickets','getUnreadSupportTickets' , 'sites' , 'bulkUpdate','rate')]);

        $this->model = new SupportTicket();     
    }

    public function index()
	{
         $urlValues= Input::all();
        // dd($urlValues);
         foreach( $urlValues as $key => $val )
         {
             if($val=='')
             {
                 return array("count" => 0,"tickets"=>[],"agents"=>[]);
             }
         }
         
        if(Input::has('orderBy')){
            $dateOrder = Input::get('orderBy'); // Get search order.
            Input::merge(array('orderBy' => null)); // Replace Input::get('orderBy') with NULL.
        }else{
            $dateOrder = 'desc';
        }

        $user = \Auth::user();

        $query = $this->filter();
        $response = [];

		$site_ids = $user->sitesWithCapability( 'manage_support_tickets', false );

		if( \Input::has('sites') && !empty( \Input::get('sites') ) )
		{
			$sites = explode( ',', \Input::get('sites') );

			foreach( $sites as $key => $val )
			{
				if( !in_array( $val, $site_ids ) )
				{
					unset( $sites[ $key ] );
				}
			}

			if( !empty( $sites ) )
				$site_ids = $sites;
		}

		if( \Input::has('agents') && ( !empty( \Input::get('agents') ) || \Input::get('agents') == 0 ) )
		{
			$agent_ids = explode( ',', \Input::get('agents' ) );

			if( !empty( $agent_ids ) )
				$query = $query->whereIn( 'agent_id', $agent_ids );
		}

		if( !empty( $site_ids ) )
		{
			$query = $query->where( function ( $query2 ) use ( $site_ids )
			{
				$query2->whereIn( 'site_id', $site_ids )
					->orWhereIn( 'escalated_site_id', $site_ids );
			} );
		}
		else
		{
			$query = $query->where(function($query2) {
				$query2->whereSiteId($this->site->id)->orWhere('escalated_site_id', $this->site->id);
			} );
		}



		$p = \Input::get('p');
        if(\Input::get('q'))
            $p=1;
		if($p!=null)
		{
			$response['count'] = $query->whereParentId(0)->count();
            if(\Input::get('q'))
                $response['items'] = $query->skip((Input::get('p')-1)*config("vars.default_page_size"))->with(array('agent'))->orderBy('created_at' , $dateOrder)->whereParentId(0)->get();
			$response['tickets'] = $query->skip((Input::get('p')-1)*config("vars.default_page_size"))->with(array('agent'))->orderBy('created_at' , $dateOrder)->whereParentId(0)->get();
			$response['agents'] = $query->with(array('agent'))->whereParentId(0)->groupBy('agent_id')->get(["agent_id"]);
            foreach ($response['tickets'] as $key => $value) {
			   // $response['tickets'][$key]['lastReply']=SupportTicket::whereParentId($value->id)->orderBy('updated_at' , 'DESC')->first(['updated_at','created_at']);

				if( !empty( $response['tickets'][$key]->agent ) && empty( $response['tickets'][$key]->agent->profile_image ) )
					$response['tickets'][$key]->agent->profile_image = User::gravatarImage( $response['tickets'][$key]->agent->email, 100 );

				if( !empty( $response['tickets'][$key]->user_email ) && empty( $response['tickets'][$key]->profile_image ) )
					$response['tickets'][$key]->profile_image = User::gravatarImage( $response['tickets'][$key]->user_email, 300 );

				if( $response['tickets'][$key]->escalated_site_id == 2056 )
					$response['tickets'][$key]->sm_tech = true;
				elseif( $response['tickets'][$key]->escalated_site_id == 6325 )
					$response['tickets'][$key]->sm_marketing = true;
			}


			return $response;
		}
		else
			return $this->model->with(['agent' , 'notes'])->whereSiteId($this->site->id)->whereParentId(0)->orderBy('updated_at','DESC')->get();
	}

    public function update($model){
        //return ($model);
        $model = parent::update($model);
        //$current_company_id = Company::getOrSetCurrentCompany();

        if($model->agent_id!=0){
            $role = User::find($model->agent_id);
            $model->agent = $role;
            if( !empty( $model->agent ) && empty( $model->agent->profile_image ) )
                $model->agent->profile_image = User::gravatarImage( $model->agent->email );
        }

		if( $model->user_id != 0 ){
			$model->user = User::find($model->user_id);

			if( !empty( $model->user ) && empty( $model->user->profile_image ) )
				$model->user->profile_image = User::gravatarImage( $model->user->email );
		}

        return $model;
    }

    public function show($model){
        if( $model->parent_id != 0 )
            \App::abort('403','That is an invalid ticket number.');

        $model = $this->model->with(['user' , 'reply' , 'actions', 'actions.user', 'reply.user' , 'notes', 'notes.user' , 'agent'])->find($model->id);

        if( !empty( $model->user ) && empty( $model->user->profile_image ) )
            $model->user->profile_image = User::gravatarImage( $model->user->email );

        if( !empty( $model->agent ) && empty( $model->agent->profile_image ) )
            $model->agent->profile_image = User::gravatarImage( $model->agent->email );

		if( $model->escalated_site_id == 2056 )
			$model->sm_tech = true;
		elseif( $model->escalated_site_id == 6325 )
			$model->sm_marketing = true;

        foreach( $model->reply as $reply )
        {
            if( !empty( $reply->user ) && empty( $reply->user->profile_image ) )
                $reply->user->profile_image = User::gravatarImage( $reply->user->email );
        }

        $this->site = Site::find( $model->site_id );

        $this->user = \Auth::user();

        if( $model->user_id == $this->user->id )
            $has_access = true;

        $recent_tickets = SupportTicket::whereSiteId($model->site_id)->whereUserId($model->user_id)->whereParentId(0)->where('id','!=',$model->id)->orderBy('created_at','DESC')->take(3)->get();
       
        $advanced_info = [];

        if( $this->site )
            $access_passes = Role::with('accessLevel')->whereSiteId($this->site->id)->whereUserId($model->user_id)->whereNotNull('access_level_id')->get();
        if ($access_passes->count() > 0)
            $advanced_info['access_levels'] = $access_passes->lists('accessLevel');
        else
            $advanced_info['access_levels'] = [];

        $admin_roles = Role::whereUserId($model->user_id)->where('type','!=','member')->get();

        $member_roles = Role::whereUserId($model->user_id)->where('type','member')->get();

        $ids = $admin_roles->lists('site_id');

        $admin = Site::whereIn('id', $ids)
                      ->get();

        $ids = $member_roles->lists('site_id');

        $members = Site::whereIn('id', $ids)
                       ->get();
        $advanced_info['member'] = $members;
        $advanced_info['admin'] = $admin;

        if( !empty( $model->site_id ) )
            $model->site = Site::find( $model->site_id );

		if( $model->reply )
		{
			foreach( $model->reply as $reply )
			{
				if( $reply->attachment )
				{
					$result = $this->getRemoteFilesize( $reply->attachment );

					$reply->attachment_size = $result != -1 ? $this->bytesToSize( $result, 0 ) : 0;
				}
			}
		}

		if( $model->attachment )
		{
			$result = $this->getRemoteFilesize( $model->attachment );

			$model->attachment_size = $result != -1 ? $this->bytesToSize( $result, 0 ) : 0;
		}

        return array('ticket'=>$model , 'recent_tickets'=>$recent_tickets , 'advanced_info'=>$advanced_info);
    }

	private function getRemoteFilesize( $url )
	{
		$result = -1;

		$curl = curl_init( $url );

		// Issue a HEAD request and follow any redirects.
		curl_setopt( $curl, CURLOPT_NOBODY, true );
		curl_setopt( $curl, CURLOPT_HEADER, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:27.0) Gecko/20100101 Firefox/27.0' );

		$data = curl_exec( $curl );
		curl_close( $curl );

		if( $data ) {
			$content_length = "unknown";
			$status = "unknown";

			if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
				$status = (int)$matches[1];
			}

			if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
				$content_length = (int)$matches[1];
			}

			// http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
			if( $status == 200 || ($status > 300 && $status <= 308) ) {
				$result = $content_length;
			}
		}

		return $result;
	}

	private function bytesToSize($bytes, $precision = 2 )
	{
		$kilobyte = 1024;
		$megabyte = $kilobyte * 1024;
		$gigabyte = $megabyte * 1024;
		$terabyte = $gigabyte * 1024;

		if (($bytes >= 0) && ($bytes < $kilobyte)) {
			return $bytes . ' B';

		} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
			return round($bytes / $kilobyte, $precision) . ' KB';

		} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
			return round($bytes / $megabyte, $precision) . ' MB';

		} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
			return round($bytes / $gigabyte, $precision) . ' GB';

		} elseif ($bytes >= $terabyte) {
			return round($bytes / $terabyte, $precision) . ' TB';
		} else {
			return $bytes . ' B';
		}
	}

    public function store(){
        $site = Input::get('site_id');
        $send_email = Input::get('send_email');
        
        if(isset($site)){
            $this->site = Site::find(Input::get('site_id'));
        }
        if( !SMAuthenticate::set() )
        {
            $this->user = User::whereEmail( \Input::get('user_email') )->first();

			if( !$this->user )
			{
				$primaryAccount = LinkedAccount::where('linked_email', \Input::get('user_email') )
					->where('verified', 1)
					->first();

				if ($primaryAccount)
				{
					$this->user = User::find($primaryAccount->user_id);
				}
			}

            if( !$this->user )
                $this->user = \App\Models\Transaction::createUserForTransaction( array( 'email' => \Input::get('user_email'), 'site_id' => $this->site->id ), true );
        }
        else
        {
            $this->user = \Auth::user();
        }
        //dd($this->site);
        if( !empty( $this->site ) && !SMAuthenticate::isMember($this->site->id)) {
            $this->site->addMember( $this->user );
        }

        if ( !empty( $this->site ) && ( $this->site->id == 2 || $this->site->id == 3434 || $this->site->id == 935 ) )
        {
            //$this->user = \Auth::user();
            $email = new SendGrid\Email();
            $email->setFrom($this->user->email);
            $email->setFromName($this->user->first_name . ' ' . $this->user->last_name);
            $email->setReplyTo($this->user->email);
            $email->addTo("support@smartmember.com");
            $email->setSubject(\Input::get('subject'));
            $email->setHtml(\Input::get('message'));
            SendGridEmail::sendEmail($email, true, $this->site);
        } else if (!empty($this->site) && ($this->site->id == 7012 || $this->site->id == 7042 || $this->site->id == 7059 || $this->site->id == 7087 || $this->site->id == 7219 || $this->site->id == 7258)) {
            $email = new SendGrid\Email();
            $email->setFrom($this->user->email);
            $email->setFromName($this->user->first_name . ' ' . $this->user->last_name);
            $email->setReplyTo($this->user->email);
            $email->addTo("hanfansupport@hanfanapproved.com");
            $email->setSubject(\Input::get('subject'));
            $email->setHtml(\Input::get('message'));
            SendGridEmail::sendEmail($email, true, $this->site);
        }
        else {

            //if input has email field and user with that email exists, set user_id of ticket
            if(\Input::has('user_email')){
                $user = User::whereEmail(\Input::get('user_email'))->first();
                if($user)
                    \Input::merge(array('user_id' => $user->id , 'user_name'=>$user->first_name. ' ' . $user->last_name , 'user_email'=>$user->email));
            }
            else if(SMAuthenticate::set())
                \Input::merge(array('user_id' => \Auth::user()->id , 'user_name'=>\Auth::user()->first_name.' '.\Auth::user()->last_name , 'user_email'=>\Auth::user()->email));

            $ticket = parent::store();

            if( !empty( $this->site ) )
            {
                $ticket->site_id = $this->site->id;
                $ticket->save();
            }
            if( !empty( \Input::get('parent_id') ) )
            {
                $parent_ticket = $this->model->with(array(
                                        'reply' => function ($query) {
                                            $query->orderBy('created_at', 'desc');
                                        } , 'reply.user' , 'user' , 'agent' ,
                                        'actions'=> function ($query) {
                                            $query->orderBy('created_at', 'desc');
                                        } , 'actions.user'))->find( \Input::get('parent_id') );
                $parent_ticket->data = [];
                //return $parent_ticket;
                $parent_ticket->data = $this->sortTickets($parent_ticket);
                //return $parent_ticket;
                if( empty( $this->site ) && !empty( $parent_ticket->site_id ) )
                    $this->site = Site::find( $parent_ticket->site_id );
                if( $parent_ticket )
                {
                    if(\Auth::user()->id!=$parent_ticket->user_id && !empty( $this->site ) )
                    {
                        if( empty( $parent_ticket->user_email ) )
                        {
                            $ticket_user = User::find( $parent_ticket->user_id );
                            if( $ticket_user )
                                $parent_ticket->user_email = $ticket_user->email;
                        }

                        if( empty( $parent_ticket->user_name ) )
                        {
                            if( empty( $ticket_user ) )
                                $ticket_user = User::find( $parent_ticket->user_id );

                            if( $ticket_user )
                                $parent_ticket->user_name = $ticket_user->first_name . ' ' . $ticket_user->last_name;
                        }
                    }
                    else if($parent_ticket->agent_id!=0 && !empty( $this->site )){
                        $agent = User::find($parent_ticket->agent_id);
                        if($agent){
                            $agent_data['name'] = $agent->first_name . ' ' . $agent->last_name;
                            $agent_data['email'] = $agent->email;
                            SendGridEmail::sendReplyAgentEmail($agent_data , $parent_ticket , $this->site);
                        }
                    }
                    unset($parent_ticket->data);
                    $parent_ticket->last_replied_at = date("Y-m-d H:i:s");

                    if( \Auth::user() && $parent_ticket->user_id == \Auth::user()->id )
                    {
                        //$parent_ticket->three_day_sent = 0;
                        //$parent_ticket->five_day_sent = 0;
                    }

                    $parent_ticket->save();
                }
            }

            $ticket->user = User::whereId($ticket->user_id)->first();
            if($ticket->parent_id==0){
                $user = $ticket->user;
                if(!$ticket->user){
                    $user = array('name'=>\Input::get('user_name') , 'email'=>\Input::get('user_email'));
                }

                 SendGridEmail::sendNewSupportEmail($user , $ticket , $this->site);
            }

            if($ticket->agent_id!=0){
                /*
                 * This is being moved to the saving function on the SupportTicket model so that the agent doesn't get e-mailed every time there is an update to the ticket
                $role = Role::find($ticket->agent_id);
                if($role){
                    $agent = User::find($role->user_id);
                    if($agent){
                        $agent_data['name'] = $agent->first_name . ' ' . $agent->last_name;
                        $agent_data['email'] = $agent->email;
                        SendGridEmail::sendNewAgentEmail($agent_data , $ticket , $this->site);
                    }
                }
                */
            }
            elseif($ticket->parent_id==0){
                $users = Role::getMembersWithCapability($ticket->site_id,'manage_support_tickets');

				$users = User::whereIn( 'id', $users )->get();

                $emails = $users->lists('email');
                if(isset($emails));
                   SendGridEmail::sendAllAgentEmail($emails , $ticket , $this->site);
            }
            $ticket->replies = [];

			if( $ticket->attachment )
			{
				$result = $this->getRemoteFilesize( $ticket->attachment );

				$ticket->attachment_size = $result != -1 ? $this->bytesToSize( $result, 0 ) : 0;
			}
            return $ticket;
        }
    }

     public function userTickets(){
		 if( !$this->site ){
			 $error = array("message" => 'You must be on a Smart Member site to access your support tickets, not http://my.smartmember.com', "code" => 500);
			 return response()->json($error)->setStatusCode(500);
		 }

        if(!SMAuthenticate::isMember($this->site->id))
            \App::abort('403','You must be a member of this site to view tickets');

        $tickets = SupportTicket::whereSiteId($this->site->id)->whereUserId(\Auth::user()->id)->whereParentId(0)->with('user')->with('reply')->orderBy('updated_at','ASC')->get();
        return $tickets;
    }

    public function filter()
    {
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->take($page_size);
        // if( empty( Input::get('status') ) || !in_array( Input::get('status'), array( 'solved', 'spam' ) ) )
        //     $query = $query->orderByRaw("CASE WHEN `last_replied_at` != '0000-00-00 00:00:00' THEN `last_replied_at`  ELSE `created_at` end DESC");
        // else
        //     $query = $query->orderBy('updated_at','DESC');
        if(!empty(\Input::get('sortBy')))
        {
            if(\Input::get('sortBy') == 'reporter')
            {
                $query = $query->orderBy('user_name','asc');
            }
            else if(\Input::get('sortBy') == 'activity')
            {
                $query = $query->orderBy('last_replied_at','desc');
            }
            else if(\Input::get('sortBy') == 'age')
            {
                $query = $query->orderBy('created_at','desc');
            }
            else if(\Input::get('sortBy') == 'agent')
            {
                $query = $query->orderBy('agent_id');
            }
        }
        $query = $query->whereNull('deleted_at');
        if(Input::get('assignment')!=null)
        {
            $input=Input::except('assignment','assignee','start_date','end_date','date','rating');
            
            if(Input::get('assignment')!='all')
                if(Input::get('assignment')=="true")
                    $query = $query->where('agent_id','!=',0);
                if(Input::get('assignment')=="false")
                    $query = $query->whereAgentId(0);
            if(Input::get('rating')!='all')
                if(Input::get('rating')=='Rated')
                {
                    $query = $query->whereNotNull('rating');
                }
                else if(Input::get('rating')=='Not rated')
                {
                    $query = $query->whereNull('rating');
                }
                else if(Input::get('rating')=='Rated good')
                {
                    $query = $query->whereRating('s');
                }
                else if(Input::get('rating')=='Rated bad')
                {
                    $query = $query->whereRating('n');
                }

            if(Input::get('assignee')!=0)
                $query = $query->whereAgentId(Input::get('assignee'));
            if(Input::get('date')=='true')
            {
                $query = $query->where('created_at','>=',new DateTime(Input::get('start_date')));
                $query = $query->where('created_at','<=',new DateTime(Input::get('end_date')));
            }
            Input::replace($input);
        }
        else
            $input=Input::all();

        foreach ($input as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
                case 'p':
                    if (Input::has('count'))
                    {
                        $query->skip((Input::get('p')-1)*$page_size);
                    }
                    break;
                case 'count':
                    $query->take((Input::get('count')));
                    break;
				case 'sites':
				case 'agents':
                case 'sortBy':
                    break;
                default:
                    if(!empty($value ))
                        $query->where($key,'=',$value);
            }
        }

        return $query;
    }

    public function filterSearch()
    {
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->take($page_size);
        $query = $query->orderBy('updated_at','ASC');
        $query = $query->whereNull('deleted_at');
        $input=null;
        if(Input::get('assignment')!=null)
        {
            $input=Input::except('assignment','assignee','start_date','end_date');
            if(Input::get('assignment')!='all')
                if(Input::get('assignment')==true)
                    $query = $query->where('agent_id','!=',0);
                if(Input::get('assignment')==false)
                    $query = $query->whereAgentId(0);
            if(Input::get('assignee')!=0)
                $query = $query->whereAgentId(Input::get('assignee'));

            $query = $query->where('created_at','>=',Input::get('start_date'));
            $query = $query->where('created_at','<=',Input::get('end_date'));
        }
        else
        {
            $input=Input::all();
        }

        foreach ($input as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
                case 'p':
                    $query->skip((Input::get('p')-1)*$page_size);
                    break;
                case 'count':
                    $query->take((Input::get('count')));
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }
        return $query;
    }

    public function rate(){
        $ticket_id = \Input::get('id');
        $hash = \Input::get('hash');
        $rating = \Input::get('r');
        //return $rating;

        if($rating!= "s" && $rating!= "n")
            \App::abort('400','Invalid rating value');

        $ticket = SupportTicket::find($ticket_id);

        if($ticket){

            $site = Site::find( $ticket->site_id );
            if( isset( $site ) )
            {
                $subdomain = $site->subdomain;
                $site_logo = $site->meta_data()->where( 'key', 'site_logo' )->select( [ 'value' ] )->first();
                $header_bg_color = $site->getHeaderBackgroundColor();
            }

            $user_email = $ticket->user_email;
            if($hash != md5($user_email.$ticket_id)){
                return View::make( "support.rate", [
                    'subdomain' => $subdomain,
                    'site_logo' => isset( $site_logo ) ? $site_logo->value : '',
                    'header_bg_color' => $header_bg_color,
                    'noaccess' => true
                ] )->render();
            }

            if($ticket->rating){
                return View::make( "support.rate", [
                    'subdomain' => $subdomain,
                    'site_logo' => isset( $site_logo ) ? $site_logo->value : '',
                    'header_bg_color' => $header_bg_color
                ] )->render();
            }

            $ticket->rating = $rating;
            $ticket->save();
            //return redirect('http://training.smartmember.dev')->with('message', 'Thank you for you feedback');
            return View::make( "support.rate", [
                'subdomain' => $subdomain,
                'site_logo' => isset( $site_logo ) ? $site_logo->value : '',
                'header_bg_color' => $header_bg_color,
                'rating' => $rating == 's' ? 'good' : 'bad'
            ] )->render();

        }else{
            return View::make( "support.rate", [
                'subdomain' => 'sm',
                'site_logo' => isset( $site_logo ) ? $site_logo->value : '',
                'header_bg_color' => '#000000',
                'noticket' => true
            ] )->render();
        }
    }

    public function sites(){
        return [ $this->site ];
    }

    public function bulkUpdate(){
        //return \Input::get('tickets');
        $tickets = \Input::get('tickets');
        $property = \Input::get('property');
        $value = \Input::get('value');

        if(!isset($tickets) || !isset($property) || !isset($value))
            return array('success'=>false , 'message'=>'Some fields missing');
        \DB::table('support_tickets')
            ->whereIn('id', \Input::get('tickets'))
            ->update([ \Input::get('property')=> \Input::get('value')]);
        $resp = SupportTicket::whereIn('id' , $tickets)->with('agent')->get();
        return array('success'=>true , 'tickets'=>$resp);
    }

    public static function sortTickets($ticket){
        $data = [];
        $count = $ticket->actions->count() > $ticket->reply->count() ? $ticket->actions->count() : $ticket->reply->count();
        $i = 0;
        $j = 0;
        //dd($ticket);
        while ( $i < $ticket->actions->count() && $j < $ticket->reply->count() ){
            $action = $ticket->actions[$i];
            $reply = $ticket->reply[$j];

            if($action->created_at > $reply->created_at){
                $data[] = $action;
                $i++;
            }
            else{
                $data[] = $reply;
                $j++;
            }

        }
        while($i < $ticket->actions->count()){
            $data[] = $ticket->actions[$i];
            $i++;
        }
        while($j < $ticket->reply->count()){
            $data[] = $ticket->reply[$j];
            $j++;
        }
        return $data;
    }

}