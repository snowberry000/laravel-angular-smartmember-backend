<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\EmailSubscriber;
use App\Models\EmailList;
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
        $this->middleware("auth", ['except' => array('postSubscribe', 'formSubscribe','unsubscribe','turnOptInToMember')]);
    }

    public function show($model){
        return EmailSubscriber::whereId($model->id)->with("emailLists")->first();
    }

    public function index()
    {
		$total_count = 0;
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->orderBy('id' , 'DESC');
        $query = $query->with('user');
		$account_id = \Auth::user()->id;
        $query = $query->with(['emailLists'=>function($q) use ($account_id){
            $q->where('email_lists.account_id',$account_id);
        }]);
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

		$total_count = $query->count() + $members->count();

		$query = $query->union( $members->getQuery() );

        foreach (\Input::all() as $key => $value){
            switch($key){
                case 'q':
				case 'view':
				case 'p':
				case 'bypass_paging':
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
                if( $value == true )
                {
                    $el = EmailList::find( $key );
                    if($el!=null)
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

    public function getEmailLists()
    {
        $emailList = [];

        $list_type = \Input::get('list_type', 'user');
        $hash = \Input::get('hash');

        $subscriber = FALSE;
        if ($list_type == 'segment')
        {
            $subscriber = User::where('email_hash', $hash)->first();
        }
        else
        {
            $subscriber = EmailSubscriber::where('hash', $hash)->first();
        }

        if ($subscriber && $subscriber->emailList)
            $emailList[] = $subscriber->emailList;

        if ($list_type != 'segment')
        {
			$account_id = \Auth::user()->id;
            $segmentList = EmailList::whereListType($list_type)->whereAccountId($account_id)->get();
            $sites = [$this->site->id];
            foreach ($segmentList as $list)
            {
                $segmentTool = new SegmentTool($list->segment_query, $sites);
                $users = $segmentTool->getUsers();
                if (in_array($subscriber->email, $users))
                {

                    $unsub = UnsubscriberSegment::where('email', $subscriber->email)
                                                ->where('list_id', $list->id)->first();
                    if ( ! $unsub)
                        $emailList[] = $list;
                }

            }
        }

        return array('site_name' => $this->site->name, 'data' => $emailList);
    }

    public function unsubscribe()
    {
        $job_id = \Input::get("job_id");

		$site_id = \Input::get('site_id', 0);

        $list_type = \Input::get("list_type", 'user');
        $hash = \Input::get('hash');

        if ($list_type == 'segment')
        {
            $subscriber = User::where('email_hash', $hash)->first();

			if( $subscriber )
			{
				UnsubscriberSegment::insert(
					[ 'email' => $subscriber->email,
					  'company_id' => $site_id ] );
			}
        }
        else
        {
            $subscriber = EmailSubscriber::where('hash', $hash)->first();

			if( $subscriber )
			{
				Unsubscriber::insert(
					[ 'subscriber_id' => $subscriber->id,
					  'job_id' => $job_id,
					  'company_id' => $site_id ] );
			}
        }

        if (! $subscriber)
			return;

        if(\Input::has('reason') && \Input::get('reason'))
        {
            \DB::table('unsubfeedback')->insert([
                ['email' => $subscriber->email, 'unsub_reason' => \Input::get('reason'),'site_id' => $site_id]
            ]);
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

        $emailList->subscribers()->attach($subscriber->id);
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
		    $emailList->subscribers()->attach($subscriber->id);
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
}
