<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\EmailRecipientsQueue;
use App\Models\EmailRecipient;
use App\Models\EmailSubscriber;
use App\Models\EmailList;
use App\Models\EmailListLedger;
use App\Models\EmailJob;
use App\Models\Company;
use App\Models\AppConfiguration\SendGridEmail;
use App\Models\Site\Role;
use App\Models\AppConfiguration\AweberIntegration;
use App\Models\Site;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\LinkedAccount;
use App\Models\Unsubscriber;
use App\Models\UnsubscriberSegment;
use App\Models\SegmentTool;
use App\Http\Controllers\Api\DB;
use App\Models\SiteMetaData;
use Carbon\Carbon;
use App\Models\AppConfiguration;
use App\Models\AppConfiguration\GetResponse;
use App\Models\AppConfiguration\ConstantContact;

class EmailSubscriberController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new EmailSubscriber();
        $this->middleware("auth", ['except' => array('postSubscribe', 'formSubscribe','unsubscribe','turnOptInToMember','getUnsubscribeInfo')]);
    }

    public function show($model){
        return EmailSubscriber::whereId($model->id)->with("emailLists")->first();
    }

    public function index()
    {
        $emailList_id = false;
        if (\Input::has('emaillist_id') && !empty(\Input::get('emaillist_id')))
        {
            $emailList_id = \Input::get('emaillist_id');
        }
		$total_count = 0;
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->orderBy('id' , 'DESC');
        $query = $query->with('user','emailLists');
		$account_id = \Auth::user()->id;
        $query = $query->whereHas('emailLists', function($q) use ($account_id, $emailList_id){
            $q->where('email_lists.account_id',$account_id);
            if ($emailList_id)
            {
               $q->where('email_listledger.list_id', $emailList_id);
            }
        });
        $query = $query->whereAccountId($account_id);
        $query = $query->select('id' , 'created_at')->selectRaw('email COLLATE utf8_general_ci as email')->selectRaw('name COLLATE utf8_general_ci as name')->selectRaw('"Subscriber" as status');
        $site_ids = [$this->site->id];

        $members = User::whereHas('role', function($q) use ($site_ids) {
            $q->where('sites_roles.type', 'member');
            $q->whereIn('site_id', $site_ids);
        })
            ->has('subscriber','<',1)
            ->select('id' , 'created_at')->selectRaw('email COLLATE utf8_general_ci as email')->selectRaw('CONCAT( first_name,  " ", last_name ) COLLATE utf8_general_ci AS name' )->selectRaw('"Member" as status');

		if( \Input::has('q') && !empty( \Input::get('q') ) )
		{
			$members = $members->where(function($q){
				$q->where( 'email', 'like', '%' . \Input::get( 'q' ) . '%' );
				$q->orwhere('first_name', 'like', '%' . \Input::get( 'q' ) . '%' );
				$q->orwhere('last_name', 'like', '%' . \Input::get( 'q' ) . '%' );
			});
			$query = $this->model->applySearchQuery($query,\Input::get('q') );
		}
        if ($emailList_id)
        {
            $total_count = $query->count();
        } else {
            $total_count = $query->count() + $members->count();
            $query = $query->union( $members->getQuery() );
        }

        foreach (\Input::all() as $key => $value){
            switch($key){
                case 'q':
				case 'view':
				case 'p':
				case 'bypass_paging':
                case 'emaillist_id':
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }

		$return = [];

		$return['total_count'] = $total_count;

		if( !\Input::has('bypass_paging') || !\Input::get('bypass_paging') )
			$query = $query->take($page_size);

		if( \Input::has('p') )
			$query->skip((\Input::get('p')-1)*$page_size);

		$return['items'] = $query->get();

		return $return;
    }

    public function store(){
        $hash = EmailSubscriber::getHash(\Input::get("email"));
		$account_id = \Auth::user()->id;
        \Input::merge(array("hash"=>$hash,'account_id'=>$account_id));
        $record = EmailSubscriber::where('email','=',\Input::get("email"))->whereAccountId($account_id)->whereNull('deleted_at')->first();
        if (empty($record->id))
        {
            $record = $this->model->create(\Input::except('token'));
        }
        else
        {
            \Input::merge(array("id" => $record->id));
            $record = $this->model->update(\Input::except('token', 'email_lists'));
            return $record;
        }
        $lists = \Input::get("lists");

        if( !empty( $lists ) && is_array( $lists ) )
        {
            foreach( $lists as $key => $value )
            {
                if( $value )
                {
                    $el = EmailList::find( $key );
                    if($el)
                    {
                        $el->total_subscribers = $el->total_subscribers + 1;
                        $el->save();
                    }
                }
            }
        }

        if (!$record->id){
            App::abort(401, "The operation requested couldn't be completed");
        }

        return array('record'=>$record,'total'=>1);
    }

    public function getUnsubscribeInfo()
    {
        $list_type = \Input::get('list_type', 'user');
        $hash = \Input::get('hash');

        $return = [];

		$site = Site::whereId( ( !empty( \Input::get('site_id') ) ? \Input::get('site_id') : ( $this->site ? $this->site->id : 1 ) ) )->with(['meta_data' => function($query){
			$query->whereIn('site_meta_data.key',['site_logo']);
		}])->first();

		if( $site )
		{
			$return['site'] = $site;
		}

        if ($list_type == 'segment')
        {
            $subscriber = User::where('email_hash', $hash)->first();

			if( $subscriber )
				$return['subscriber'] = $subscriber;
        }
        else
            $subscriber = EmailSubscriber::where('hash', $hash)->first();

		$subscriber_ids = [];

		if( $subscriber )
		{
			$subscriber_ids[] = $subscriber->id;

			$subscribers = EmailSubscriber::whereEmail( $subscriber->email )->get();

			if( $subscribers )
			{
				foreach( $subscribers as $sub )
				{
					if( !in_array( $sub->id, $subscriber_ids ) )
						$subscriber_ids[] = $sub->id;
				}
			}
		}

		if( $subscriber )
			$return['subscriber'] = $subscriber;

		$recipient_ids = EmailRecipientsQueue::whereEmailJobId( \Input::get('job_id') )->withTrashed()->get()->lists(['email_recipient_id']);

		if( $recipient_ids && count( $recipient_ids ) > 0 )
		{
			$recipients = EmailRecipient::whereIn('id', $recipient_ids )->withTrashed()->get();

			if( $recipients )
			{
				$email_list_ids = [];

				foreach( $recipients as $key => $val )
				{
					$recipient_bits = explode( '_', $val->recipient );

					if( $recipient_bits[0] == 'list' )
					{
						if( !empty( $recipient_bits[1] ) )
						{
							$email_list = EmailList::find( $recipient_bits[1] );

							if( $email_list && !empty( $email_list->account_id ) )
							{
								$email_lists = EmailList::whereAccountId( $email_list->account_id )->get()->lists(['id']);

								if( $email_lists )
								{
									foreach( $email_lists as $list )
									{
										if( !in_array( $list, $email_list_ids ) )
											$email_list_ids[] = $list;
									}
								}
							}

							if( !in_array( $recipient_bits[ 1 ], $email_list_ids ) )
								$email_list_ids[] = $recipient_bits[ 1 ];
						}
					}
				}

				if( !empty( $email_list_ids ) )
				{
					$subscribed_list_ids = EmailListLedger::whereIn( 'list_id', $email_list_ids )->whereIn( 'subscriber_id', $subscriber_ids )->get()->lists(['list_id']);

					if( $subscribed_list_ids && count( $subscribed_list_ids ) > 0 )
						$return[ 'email_lists' ] = EmailList::whereIn( 'id', $subscribed_list_ids )->get();
				}
			}
		}

        return $return;
    }

	public function unsubscribe()
	{
		$job_id = \Input::get("job_id");

		$site_id = \Input::get('site_id', 0);

		if( !$site_id && $this->site && $this->site->id )
			$site_id = $this->site->id;

		$list_type = \Input::get("list_type", 'user');
		$hash = \Input::get('hash', 'doesntexist');//set it to something we don't use by default, it will look for the e-mail address instead then

		switch( $list_type )
		{
			case 'segment':
				$subscriber = User::where('email_hash', $hash)->first();

				if( !$subscriber )
				{
					if( \Input::has('email_address') )
					{
						$subscriber = User::whereEmail( \Input::get('email_address') )->first();
					}
				}
				break;
			default:
				$subscriber = EmailSubscriber::where('hash', $hash)->first();

				if( !$subscriber )
				{
					if( \Input::has('email_address') )
					{
						$subscriber = EmailSubscriber::whereEmail( \Input::get('email_address') )->first();
					}
				}

				if( $subscriber )
				{
					Unsubscriber::insert(
						[ 'subscriber_id' => $subscriber->id,
						  'job_id' => $job_id,
						  'company_id' => $site_id ] );
				}
		}

		if( \Input::has('site_emails') && !empty( \Input::get('site_emails') ) )
		{
			UnsubscriberSegment::insert(
				[ 'email' => $subscriber->email,
				  'site_id' => $site_id ] );
		}

		if( \Input::has('lists') && !empty( \Input::get('lists') ) )
		{
			$subscribers = EmailSubscriber::whereEmail( $subscriber->email )->get()->lists(['id']);

			if( $subscribers )
			{
				foreach( \Input::get( 'lists' ) as $key => $val )
				{
					$subscriber_entry = EmailListLedger::whereListId( $val[ 'id' ] )->whereIn( 'subscriber_id', $subscribers )->get();

					if( $subscriber_entry && count( $subscriber_entry ) > 0 )
					{
						foreach( $subscriber_entry as $key2 => $val2 )
						{
							$val2->delete();
						}
					}
				}
			}
		}
	}

    public function update($model)
    {
        $model = parent::update($model);
        $subscribeToLists = [];

        if ( \Input::has('lists') )
        {
            $currentLists = $model->emailLists()->get()->lists('id')->toArray();

            foreach (\Input::get('lists') as $key => $value)
            {

                if ($value)
                {
                    if (!in_array($key, $currentLists))
                    {
                        $el = EmailList::find($key);
                        $el->total_subscribers = $el->total_subscribers + 1;
                        $el->save();
                    }

                    $subscribeToLists[] = $key;
                }
                else
                {
                     if (in_array($key, $currentLists))
                    {
                        $el = EmailList::find($key);
                        $el->total_subscribers = $el->total_subscribers - 1;
                        $el->save();
                    }
                }
            }
            $currentLists = $model->emailLists()->sync($subscribeToLists);
        }

        return $model;
    }

     public function destroy($model){
        $currentLists = $model->emailLists;
        foreach ($currentLists as $list)
        {
            $list->total_subscribers = $list->total_subscribers - 1;
            $list->save();
        }
        return parent::destroy($model);
    }

    public function unsubscribeList() {
        $list_id = \Input::get('list_id');
        $subscriber = \Input::get('subscriber');
        $subscriber_entry = EmailListLedger::whereListId( $list_id )->whereSubscriberId( $subscriber['id'] )->get();
        if( $subscriber_entry && count( $subscriber_entry ) > 0 )
        {
            foreach( $subscriber_entry as $key2 => $val2 )
            {
                $val2->delete();
            }
        }
    }

    public function postSubscribe()
    {
        if (! \Input::has('email') || ! \Input::has('list')) return array();

        $email = \Input::get('email');
        $name = \Input::has('name') ? \Input::get('name') : $email;
        $subdomain = \Input::has('subdomain') ? \Input::get('subdomain') : 'training';
        $list = \Input::get('list');

        $site = Site::where('subdomain', $subdomain)->first();

        if (! $site) return array();

        $subscriber = EmailSubscriber::firstOrNew(['email' => $email,
                                                  'site_id' => $site->id]);

        if (!$subscriber->id) {
            $subscriber->name = $name;
            $subscriber->site_id = $site->id;
        }

        $subscriber->save();

        $emailList = EmailList::firstOrNew(['name' => $list, 'site_id' => $site->id]);
        if ( !$emailList->id ) $emailList->save();

        $existingSubscriber = $emailList->subscribers()->where('subscriber_id', $subscriber->id)->first();
        if ($existingSubscriber) {
            return $subscriber;
        }

        $email_list_ledger = new EmailListLedger();
        $email_list_ledger->list_id = $list;
        $email_list_ledger->subscriber_id = $subscriber->id;
        $email_list_ledger->save();
        //EmailListLedger::insert(['list_id' => $emailList->id, 'subscriber_id' => $subscriber->id]);
        //$emailList->subscribers()->attach($subscriber->id);
        $emailList->total_subscribers = $emailList->total_subscribers + 1;
        $emailList->save();

        return $subscriber;
    }

    public function getCSV()
    {
        if(!\Auth::check())
            \App::abort(401, "You must be signed in to a team");

        $site_ids = [ $this->site->id ];

        $subscribers = [];

        if( !empty( \Input::get('segment_query') ) )
        {
            $segment_query = \Input::get('segment_query');

            $segment = new SegmentTool($segment_query, $site_ids);

            $subscribers = $segment->getUsers();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=subscriber.csv');
        header( "Pragma: no-cache" );
        header( "Expires: 0" );

        flush();

        $output = fopen('php://output','w');

        fputcsv( $output, [ 'Name','E-mail']);
        foreach ($subscribers as $subscriber)
        {
            //$lists = array();
            $user = User::whereEmail($subscriber['email'])->first();
            /*if( !empty( $subscriber->emailLists ) && count( $subscriber->emailLists ) > 0 )
            {
                foreach( $subscriber->emailLists as $list )
                    $lists[] = $list->name;
            }*/
            if($user && ( !empty( $user->first_name ) || !empty( $user->last_name ) ) )
			{
                $subscriber['name'] = $user->first_name . ' ' . $user->last_name;
            }
			else
			{
				$email_subscriber = $this->model->whereEmail($subscriber['email'])->first();

				if( $email_subscriber )
					$subscriber['name'] = $email_subscriber->name;
			}
            //return $subscriber;
            fputcsv($output, [ ( !empty( $subscriber['name'] ) ? $subscriber['name'] : '' ), $subscriber['email']]);
        }

        fclose($output);
        exit;

    }

    public function turnOptInToMember($email = null)
    {
        if (empty($email))
        {
            $email = \Input::get('email');
        }
        if (\Input::has('site_id'))
        {
            $site_id = \Input::get('site_id');
            $turn_optin_settings = SiteMetaData::whereSiteId($site_id)->whereKey('turn_optin_to_member')->first();
            if ($turn_optin_settings)
            {
                $user = User::firstOrNew(['email' => $email]);

				if( !$user->id )
				{
					$primaryAccount = LinkedAccount::where('linked_email', $email )
						->where('verified', 1)
						->first();

					if ($primaryAccount)
					{
						$user = User::find($primaryAccount->user_id);
					}
				}

                $newUser = true;
                $password  = '';
                $site = Site::find($site_id);

                if (! $user->id)
                {
                    $user->refreshToken();
                    $password = User::randomPassword();
                    $user->password = $password;
                    $user->email = $email;

					if( \Input::has('name') )
						$user->first_name = \Input::get('name');

                    $user->verified = 1;
                    $user->reset_token = md5( microtime().rand() );
                    $user->save();
                    $newUser = true;
                }

				if( \Input::has('access_levels') )
				{
					$levels = explode(',', \Input::get('access_levels') );

					if( count( $levels ) > 0 )
					{
						$granted_passes = [];

						foreach ($levels as $level)
						{
							$pass = Role::whereUserId($user->id)->whereSiteId($site->id)->whereAccessLevelId($level)->whereNull('deleted_at')->first();
							if (!$pass)
							{
								$pass = Role::create(['access_level_id' => $level,
													  'user_id' => $user->id,
													  'site_id' => $site->id
													 ]);

								if( !empty( $password ) )
									$pass->password = $password;

								$granted_passes[] = $pass;
							}
						}

						if( count( $granted_passes ) > 0 )
						{
							$site->addMember( $user, 'member', $password, true );
							SendGridEmail::sendAccessPassEmail( $granted_passes );
						}
						else
						{
							$site->addMember($user, 'member', $password);
						}
					}
				}
				else
				{
					$site->addMember($user, 'member', $password);
				}

				$user_options = \Input::except(['name','email','redirect_url','site_id','list','team','access_levels','account_id']);

				UserMeta::saveUserOption( $user_options, $user->id, $site );

				if( !empty( $user ) )
				{
					$data = $user->toArray();
					$data["access_token"] = $user->access_token;

					return $data;
				}
            }
        }
    }

    public function addSubscriberToEmailIntegration($list_id, $subscriber)
    {
        $app_configuration_instance_id = IntegrationMeta::whereKey('optin_member_list_id')->whereValue($list_id)->first();
        $app_configuration_instance = AppConfiguration::with(['site','account','meta_data'])->find($app_configuration_instance_id);

        switch ($app_configuration_instance->type)
        {
            /*case 'aweber':
                AweberAppConfiguration::addMemberToList($app_configuration_instance->meta_data->optin_member_list_id, $subscriber, $app_configuration_instance->remote_id, $app_configuration_instance->access_token);
                break;*/
            case 'getresponse':
                GetResponse::addMemberToList($list_id, $subscriber, $app_configuration_instance->remote_id);
                break;
            case 'constantcontact':
                ConstantContact::addMemberToList($list_id, $subscriber, $app_configuration_instance->account->access_token);
                break;
        }
    }


    public function formSubscribe()
    {
        //if (! \Input::has('email') || ! \Input::has('list'))
	    if (! \Input::has('email') )
	        return array();

        $email = \Input::get('email');
        $name = \Input::has('name') ? \Input::get('name') : $email;
        $list = \Input::get('list');
        $account_id = \Input::has('account_id') ? \Input::get('account_id') : '';

		if( empty( $account_id ) )
		{
			if( \Input::has('team') && !empty( \Input::get('team') ) )
			{
				$company = Company::find( \Input::get('team') );

				if( $company )
					$account_id = $company->user_id;
			}
		}

        $subscriber = EmailSubscriber::firstOrNew(['email' => $email, 'name' => $name, 'account_id' => $account_id]);

        if (!$subscriber->id) {
            $subscriber->name = $name;
            $subscriber->account_id = $account_id;
        }

        $subscriber->save();

        if (is_numeric($list))
        {
            $emailList = EmailList::find($list);
        } else if( $list ) {
            $emailList = EmailList::firstOrNew(['name' => $list, 'account_id' => $account_id]);
        }
        if ( $list && !$emailList->id )
	        $emailList->save();

	    if( $list )
	    {
		    $existingSubscriber = $emailList->subscribers()->where('subscriber_id', $subscriber->id)->first();

		    if ($existingSubscriber) {
			    $data = $this->turnOptInToMember($email);
			    if (\Input::has('i_email'))
			    {
				    $this->addSubscriberToEmailIntegration(\Input::get('i_email'), $subscriber);
			    }
			    if (\Input::has('redirect_url') && ( !empty( \Input::get('redirect_url') ) || \Input::get('redirect_url') == 'undefined' ) )
			    {
				    return redirect(\Input::get('redirect_url'));
			    } else {
				    return !empty( $data ) ? $data : $subscriber;
			    }
		    }

	    }


	    if( $list )
	    {
            $email_list_ledger = new EmailListLedger();
            $email_list_ledger->list_id = $list;
            $email_list_ledger->subscriber_id = $subscriber->id;
            $email_list_ledger->save();
            //EmailListLedger::insert(['list_id' => $list, 'subscriber_id' => $subscriber->id]);
		    //$emailList->subscribers()->attach($subscriber->id);
		    $emailList->total_subscribers = $emailList->total_subscribers + 1;
		    $emailList->save();

	    }

        /*
         * This part turn opt in into member if that is set
         */

        $data = $this->turnOptInToMember($email);
        if (\Input::has('i_email'))
        {
            $this->addSubscriberToEmailIntegration(\Input::get('i_email'), $subscriber);
        }

        if (\Input::has('redirect_url') && ( !empty( \Input::get('redirect_url') ) || \Input::get('redirect_url') == 'undefined' ) )
        {
            return redirect(\Input::get('redirect_url'));
        } else {
			return !empty( $data ) ? $data : $subscriber;
        }
    }

	public function clearWhiteSpaceFromEmails()
	{
		$count = EmailSubscriber::count();
		$take = 1000;

		for( $x = 0; $x < $count; $x = $x + $take )
		{
			$subscribers = EmailSubscriber::take( $take )->skip( $x )->get();

			foreach( $subscribers as $subscriber )
				$subscriber->save();
		}

		dd('we finished');
	}
}
