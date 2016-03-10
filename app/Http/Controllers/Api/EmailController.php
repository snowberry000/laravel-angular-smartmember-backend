<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Email;
use App\Models\AppConfiguration\SendGridEmail;
use App\Models\EmailQueue;
use App\Models\EmailHistory;
use App\Models\EmailList;
use App\Models\EmailListLedger;
use App\Models\EmailSubscriber;
use App\Models\Click;
use App\Models\Open;
use App\Models\Link;
use App\Models\Site;
use App\Models\User;
use App\Models\Site\Role;
use App\Models\EmailSetting;
use App\Models\Unsubscriber;
use App\Models\AccessLevel\Pass;
use Carbon\Carbon;
use Input;
use Auth;


class EmailController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Email();
    }

    public function store()
    {
		$email = Email::create( [
				'content' => \Input::get( 'content', '' ),
				'subject' => \Input::get( 'subject', 'No Subject' ),
				'mail_reply_address' => \Input::has('mail_reply_address') ? \Input::get('mail_reply_address') : '',
				'sendgrid_integration' => \Input::has('sendgrid_integration') ? \Input::get('sendgrid_integration') : 0,
				'mail_signature' => \Input::has('mail_signature') ? \Input::get('mail_signature') : '',
				'mail_name' => \Input::has('mail_name') ? \Input::get('mail_name') : '',
				'mail_sending_address' => \Input::has('mail_sending_address') ? \Input::get('mail_sending_address') : '',
				'site_id' => $this->site->id,
				'recipient_type' => \Input::has('recipient_type') ? \Input::get('recipient_type') : ''
		] );

        \Input::merge(['site_id' => $this->site->id]);
        $email->processAction(\Input::all());

		return Email::whereId( $email->id )->with('recipients')->first();
    }

    public function sendTestEmail()
    {
		\Input::merge(['site_id' => $this->site->id]);

		$emailSetting = EmailSetting::where( 'site_id', $this->site->id )->first();
		if ($emailSetting)
			$mail_signature = $emailSetting->mail_signature;
		else
			$mail_signature = '';

		$content = \Input::get('content','');

		$content = $content . ( \Input::has('mail_signature') && !empty( \Input::get('mail_signature') ) ? \Input::get('mail_signature') : $mail_signature );
		$content = Email::AddUnsubscribeToContent( $content, $this->site->id );
		$content = Open::AddPixelToContent($content);
		$content = str_replace( '%subscriber_name%', !empty( \Auth::user()->first_name ) ? \Auth::user()->first_name : '[USER NAME WILL APPEAR HERE]', $content );
		$content = str_replace( '%subscriber_email%', !empty( \Auth::user()->email ) ? \Auth::user()->email : '[USER EMAIL WILL APPEAR HERE]', $content );

		preg_match_all("|%[a-zA-Z0-9-_]+%|U", $content, $matches, PREG_PATTERN_ORDER);

		$additional_meta = [];

		$items_to_skip = [
			'%hash%',
			'%subscriber_id%',
			'%subscriber_name%',
			'%list_type%'
		];

		if( !empty( $matches[0] ) && is_array( $matches[0] ) )
		{
			foreach( $matches[0] as $match )
			{
				if( !in_array( $match, $items_to_skip ) )
					$additional_meta[] = $match;
			}

			if( !empty( $additional_meta ) )
			{
				$meta_data = [ ];

				$meta_data_raw = \Auth::user()->meta( $this->site->id )->get();
				if( !( $meta_data_raw->isEmpty() ) )
				{
					foreach( $meta_data_raw as $meta_key => $meta_val )
					{
						if (!empty($meta_val->value))
						{
							$meta_data[ '%' . $meta_val->key . '%' ] = $meta_val->value;
						} else {
							$meta_data[ '%' . $meta_val->key . '%' ] = 0;
						}

					}
				}  else {
					$meta_data['%aid%'] = 0;
				}
				$replacements = [];
				foreach( $additional_meta as $item )
				{
					if( !empty( $meta_data[ $item ] ) )
						$replacements[ $item ] = $meta_data[ $item ];
					else
						$replacements[ $item ] = '';
				}

				if( !empty( $replacements ) )
					$content = str_replace( array_keys( $replacements ), array_values( $replacements ), $content );
			}
		}

		$email_count = 0;
		if( \Input::has('recipient_type') && \Input::get('recipient_type') == 'segment' && \Input::has('intros') && !empty(\Input::get('intros')) )
		{
			foreach( \Input::get('intros') as $intro )
			{
				if( !empty( $intro['intro'] ) )
				{
					$intro['intro'] = str_replace( '%subscriber_name%', !empty( \Auth::user()->first_name ) ? \Auth::user()->first_name : '[USER NAME WILL APPEAR HERE]', $intro['intro'] );
					$intro['intro'] = str_replace( '%subscriber_email%', !empty( \Auth::user()->email ) ? \Auth::user()->email : '[USER EMAIL WILL APPEAR HERE]', $intro['intro'] );
					preg_match_all("|%[a-zA-Z0-9-_]+%|U", $intro['intro'], $matches, PREG_PATTERN_ORDER);

					$additional_meta = [];

					$items_to_skip = [
						'%hash%',
						'%subscriber_id%',
						'%subscriber_name%',
						'%list_type%'
					];

					if( !empty( $matches[0] ) && is_array( $matches[0] ) )
					{
						foreach( $matches[0] as $match )
						{
							if( !in_array( $match, $items_to_skip ) )
								$additional_meta[] = $match;
						}

						if( !empty( $additional_meta ) )
						{
							$meta_data = [ ];

							$meta_data_raw = \Auth::user()->meta( $this->site->id )->get();

							if( !( $meta_data_raw->isEmpty() ) )
							{
								foreach( $meta_data_raw as $meta_key => $meta_val )
								{
									if (!empty($meta_val->value))
									{
										$meta_data[ '%' . $meta_val->key . '%' ] = $meta_val->value;
									} else {
										$meta_data[ '%' . $meta_val->key . '%' ] = 0;
									}

								}
							}  else {
								$meta_data['%aid%'] = 0;
							}

							$replacements = [];
							foreach( $additional_meta as $item )
							{
								if( !empty( $meta_data[ $item ] ) )
									$replacements[ $item ] = $meta_data[ $item ];
								else
									$replacements[ $item ] = '';
							}

							if( !empty( $replacements ) )
								$intro['intro'] = str_replace( array_keys( $replacements ), array_values( $replacements ), $intro['intro'] );
						}
					}
				}

				$subject = !empty( $intro['subject'] ) ? $intro['subject'] : \Input::get('subject');
				$custom_content = ( !empty( $intro['intro'] ) ? $intro['intro'] : '' ) . $content;

				$email = new Email(['content' => $custom_content, 'subject' => $subject, 'site_id' => $this->site->id]);

				SendGridEmail::sendTestEmail( \Input::get('admin'), $email->subject, $email->content, $this->site );
				$email_count++;
			}
		}
		else
		{
			$email = new Email(['content' => $content, 'subject' => \Input::get('subject', 'No Subject'), 'site_id' => $this->site->id]);

			SendGridEmail::sendTestEmail( \Input::get('admin'), $email->subject, $email->content, $this->site );
			$email_count++;
		}

        return array('success'=>1,'count'=>$email_count);
    }

    public function show($model)
    {
        return $model->whereId( $model->id )->with('recipients')->first();
    }

    public function update($model)
    {
        \Input::has('content') && $model->content = \Input::get('content');
        \Input::has('subject') && $model->subject = \Input::get('subject');
        $model->mail_reply_address = \Input::has('mail_reply_address') ? \Input::get('mail_reply_address') : '';
        $model->mail_signature = \Input::has('mail_signature') ? \Input::get('mail_signature') : '';
        $model->mail_name = \Input::has('mail_name') ? \Input::get('mail_name') : '';
        $model->mail_sending_address = \Input::has('mail_sending_address') ? \Input::get('mail_sending_address') : '';
		$model->sendgrid_integration = \Input::has('sendgrid_integration') ? \Input::get('sendgrid_integration') : 0;
		$model->recipient_type = \Input::has('recipient_type') ? \Input::get('recipient_type') : '';

        $model->save();

        \Input::merge(['site_id' => $this->site->id]);

        $model->processAction(\Input::all());

    	return Email::whereId( $model->id )->with('recipients')->first();
    }

    public function index()
    {
        if(!isset($this->site))
            return [];
        // $page_size = config("vars.default_page_size");
        // $query = $this->model;
        // $query = $query->take($page_size);
        // $query = $query->orderBy('id' , 'DESC');
        // $query = $query->whereNull('deleted_at');
        // $query = $query->with('email_jobs');
        // $query = $query->whereSiteId($this->site->id);
        // foreach (Input::all() as $key => $value){
        //     switch($key){
        //         case 'q':
        //             $query = $this->model->applySearchQuery($query,$value);
        //             break;
        //         case 'p':
        //             $query->skip((Input::get('p')-1)*$page_size);
        //             break;
        //         default:
        //             $query->where($key,'=',$value);
        //     }
        // }
        // $emails = $query->get();
        // return array('items' => $emails, 'total_count' => 12);
        \Input::merge(['site_id'=>$this->site->id]);
        return parent::paginateIndex();
    }

	public function getSegments()
	{
		$segments = [];
		$list_controller = new \App\Http\Controllers\Api\EmailListController();
		$email_lists = $list_controller->sendMailLists();

		foreach( $email_lists as $list )
		{
			$subscriber_count = EmailListLedger::join('email_subscribers', 'email_subscribers.id','=','email_listledger.subscriber_id')
				->where('email_listledger.list_id', $list->id)
				->select('email_listledger.subscriber_id')
				->distinct()
				->get()
				->count();

			if( intval( $list->total_subscribers ) != $subscriber_count )
			{
				$list->total_subscribers = $subscriber_count;
				$list->save();
			}

			$segments[] = array(
				'type' => 'list',
				'name' => $list->name,
				'count' => $subscriber_count,
				'target_id' => $list->id
			);
		}

		$count = Role::join('users','users.id','=','sites_roles.user_id')
			->whereNull('users.deleted_at')
			->where( 'sites_roles.site_id', $this->site->id )
			->select('sites_roles.user_id')
			->distinct()
			->get()
			->count();

		if( intval( $this->site->total_members ) != $count )
		{
			$this->site->total_members = $count;
			$this->site->save();
		}

		$segments[] = array(
			'type' => 'site',
			'name' => $this->site->name,
			'subdomain' => $this->site->subdomain,
			'domain' => $this->site->domain,
			'count' => $count,
			'target_id' => $this->site->id
		);

		$site_ids[] = $this->site->id;

		$level_controller = new \App\Http\Controllers\Api\AccessLevelController();
		$levels = $level_controller->sendMailAccessLevels();

		foreach( $levels as $level )
		{
			$user_count = Role::whereAccessLevelId( $level->id )->where(function($q){
				$q->whereNull('expired_at');
				$q->orwhere('expired_at','0000-00-00 00:00:00');
				$q->orwhere('expired_at','>', Carbon::now()->timestamp );
			})->count();

			$segments[] = array(
				'type' => 'level',
				'name' => $level->name,
				'count' => $user_count,
				'site' => $level->site_id,
				'target_id' => $level->id
			);
		}

		$total_available = 0;

		if( !empty( $site_ids ) )
		{
			$user_count = Role::join( 'users', 'users.id', '=', 'sites_roles.user_id' )
				->whereNull( 'users.deleted_at' )
				->whereIn( 'sites_roles.site_id', $site_ids )
				->selectRaw( 'COUNT( DISTINCT users.email ) as total_count' )
				->get();

			$total_available += $user_count[0]->total_count;

			$email_lists = $list_controller->sendMailLists();

			$list_ids = [];

			foreach( $email_lists as $list )
				$list_ids[] = $list->id;

			$user_id = \Auth::user()->id;

			$site_id = $this->site->id;

			$total_available += EmailSubscriber::where( function( $q) use ($user_id, $list_ids, $site_id) {
					$q->whereIn('email_listledger.list_id', $list_ids );
					$q->orwhere('email_subscribers.account_id', $user_id );
					$q->orwhere('email_subscribers.site_id', $site_id );
				} )
				->leftjoin( 'email_listledger', 'email_listledger.subscriber_id', '=', 'email_subscribers.id')
				->leftjoin( 'users', 'users.email', '=', 'email_subscribers.email' )
				->leftjoin( 'sites_roles', function ( $join ) use ( $site_ids )
				{
					$join->on( 'users.id', '=', 'sites_roles.user_id' );
					$join->whereIn( 'sites_roles.site_id', $site_ids );
				} )
				->where(function($q){
					$q->whereNull( 'users.email' );
					$q->orwhere(function($query){
						$query->whereNull( 'sites_roles.id');
					});
				})
				->whereNull( 'users.deleted_at' )
				->whereNull( 'sites_roles.deleted_at' )
				->whereNull( 'email_listledger.deleted_at' )
				->whereNull( 'email_subscribers.deleted_at' )
				->select( 'email_subscribers.id' )
				->distinct()
				->count();
		}

		$segments[] = array(
			'type' => 'catch_all',
			'name' => 'All Users and Subscribers',
			'count' => $total_available,
			'target_id' => 'catch_all'
		);

		return $segments;
	}

	public function calculateSubscribers()
	{
		/**
		 * This function is currently highly inefficient
		 *
		 * It needs to be updated to only query for new recipients per segment, excluding recipients that already matched a previous segment
		 * and then just do ->count()
		 *
		 * the trick is that each segment has to be compared against all segments that came before it to see how many new recipients there are from that
		 * segment that didn't match any of the previous segments
		 *
		 * the cludge is just to grab all the e-mails for each segment and find which ones weren't in the list prior to that segment
		 */
		$emails = [];

		$return = ['segments'=>[], 'total' => 0];
		if( \Input::has('segments') )
		{
			foreach( \Input::get('segments') as $segment )
			{

				switch( $segment['type'] )
				{
					case 'site':
						$new_emails = User::join('sites_roles as r', 'users.id', '=', 'r.user_id' )
							->where('r.site_id', $segment['target_id'])
							->whereNull('r.deleted_at')
							->select('users.email')
							->distinct()
							->lists('email')
							->toArray();
						break;
					case 'list':
						$new_emails = EmailListLedger::join('email_subscribers', 'email_subscribers.id','=','email_listledger.subscriber_id')
							->where( 'email_listledger.list_id', $segment['target_id'] )
							->whereNull( 'email_subscribers.deleted_at')
							->whereNull( 'email_listledger.deleted_at')
							->select('email')
							->distinct()
							->lists('email')
							->toArray();
						break;
					case 'level':
						$new_emails = User::join('access_passes as ap', 'users.id', '=', 'ap.user_id')
							->where('ap.access_level_id', $segment['target_id'])
							->whereNull('ap.deleted_at')
							->select('users.email')
							->distinct()
							->lists('email')
							->toArray();
						break;
					case 'catch_all':
						$site_id = $this->site->id;
						$new_emails = Role::join('users','users.id','=','sites_roles.user_id')
							->whereNull('users.deleted_at')
							->where( 'sites_roles.site_id', $site_id )
							->select('users.email')
							->distinct()
							->lists('users.email')
							->toArray();

						$email_lists = EmailList::whereAccountId( \Auth::user()->id )->get();

						$list_ids = [];

						foreach( $email_lists as $list )
							$list_ids[] = $list->id;

						$user_id = \Auth::user()->id;

						$extra_emails = EmailSubscriber::where( function( $q) use ($user_id, $list_ids, $site_id) {
								if( !empty( $list_ids ) )
								{
									$q->whereIn( 'email_listledger.list_id', $list_ids );
									$q->orwhere( 'email_subscribers.account_id', $user_id );
								}
								else
								{
									$q->where( 'email_subscribers.account_id', $user_id );
								}

								$q->orwhere('email_subscribers.site_id', $site_id );
							} )
							->leftjoin( 'email_listledger', 'email_listledger.subscriber_id', '=', 'email_subscribers.id')
							->leftjoin( 'users', 'users.email', '=', 'email_subscribers.email' )
							->leftjoin( 'sites_roles', function ( $join ) use ( $site_id )
							{
								$join->on( 'users.id', '=', 'sites_roles.user_id' );
								$join->where( 'sites_roles.site_id', '=', $site_id );
							} )
							->where(function($q){
								$q->whereNull( 'users.email' );
								$q->orwhere(function($query){
									$query->whereNull( 'sites_roles.id');
								});
							})
							->whereNull( 'users.deleted_at' )
							->whereNull( 'sites_roles.deleted_at' )
							->whereNull('email_subscribers.deleted_at')
							->whereNull('email_listledger.deleted_at')
							->select('email_subscribers.email')
							->distinct()
							->lists('email_subscribers.email')
							->toArray();

						$new_emails = array_unique( array_merge( $new_emails, $extra_emails ) );
						break;
				}

				$duplicates = count( $new_emails ) - count( array_diff( $new_emails, $emails ) );

				$emails = array_unique( array_merge( $emails, $new_emails ) );

				$return['segments'][ $segment['type'] . '_' . $segment['target_id'] ] = array(
					'duplicates' => $duplicates > 0 ? $duplicates : 0,
					'count' => count( $new_emails )
				);
			}
		}

		$return['total'] = count( $emails );

		return $return;
	}

	public function duplicatesOnlyCalculateSubscribers()
	{
		$return = ['segments'=>[], 'total' => 0];
		if( \Input::has('segments') )
		{
			$total = 0;

			$sites = $lists = $levels = [];

			foreach( \Input::get('segments') as $segment )
			{
				$new_total = 0;

				$users = new User();

				if( !empty( $sites ) )
				{
					$users = $users->leftjoin('roles as r', 'users.id', '=', 'r.user_id' );

					$users = $users->where(function($q) use ($sites){
						$q->whereIn('r.site_id', $sites );
						$q->whereNull('r.deleted_at');
					});
				}

				if( !empty( $levels ) )
				{
					$users = $users->leftjoin('access_passes as ap', 'users.id', '=', 'ap.user_id');
					if( !empty( $sites ) )
					{
						$users = $users->orwhere(function($q) use ($levels) {
							$q->whereIn('ap.access_level_id', $levels );
							$q->whereNull('ap.deleted_at');
						});
					}
					else
					{
						$users = $users->where(function($q) use ($levels) {
							$q->whereIn('ap.access_level_id', $levels );
							$q->whereNull('ap.deleted_at');
						});
					}
				}

				if( !empty( $lists ) )
				{
					$users = $users->leftjoin('email_subscribers as s', 'users.email', '=', 's.email' )
								   ->leftjoin('email_listledger as el', 's.id', '=', 'el.subscriber_id' );

					if( !empty( $sites ) || !empty( $levels ) )
					{
						$users = $users->orwhere( function ( $q ) use ( $lists )
						{
							$q->whereIn( 'el.list_id', $lists );
							$q->whereNull( 's.deleted_at' );
							$q->whereNull( 'el.deleted_at' );
						} );
					}
					else
					{
						$users = $users->where( function ( $q ) use ( $lists )
						{
							$q->whereIn( 'el.list_id', $lists );
							$q->whereNull( 's.deleted_at' );
							$q->whereNull( 'el.deleted_at' );
						} );
					}

					$subscribers = EmailSubscriber::leftjoin('users as u', 'email_subscribers.email', '=', 'u.email')
						->leftjoin('email_listledger as el', 'email_subscribers.id', '=', 'el.subscriber_id')
						->whereNull('u.email')
						->whereNull('el.deleted_at')
						->whereIn('el.list_id', $lists )
						->selectRaw( 'COUNT(DISTINCT email_subscribers.email) as total_count');
				}

				if( !empty( $sites ) || !empty( $levels ) || !empty( $lists ) )
				{
					switch( $segment['type'])
					{
						case 'site':
							$users = $users->leftjoin('roles as r2', 'users.id', '=', 'r2.user_id' );

							$users = $users->where(function($q) use ($segment){
								$q->where('r2.site_id', $segment['target_id'] );
								$q->whereNull('r2.deleted_at');
							});

							break;
						case 'level':
							$users = $users->leftjoin('access_passes as ap2', 'users.id', '=', 'ap2.user_id');

							$users = $users->where(function($q) use ($segment) {
								$q->where('ap2.access_level_id', $segment['target_id'] );
								$q->whereNull('ap2.deleted_at');
							});

							break;
						case 'list':
							$users = $users->leftjoin('email_subscribers as s2', 'users.email', '=', 's2.email' )
								->leftjoin('email_listledger as el2', 's2.id', '=', 'el2.subscriber_id' );

							$users = $users->where( function ( $q ) use ( $segment )
							{
								$q->where( 'el2.list_id', $segment['target_id'] );
								$q->whereNull( 's2.deleted_at' );
								$q->whereNull( 'el2.deleted_at' );
							} );

							if( !empty( $lists ) )
							{
								$subscribers = $subscribers->leftjoin( 'email_listledger as el2', 'email_subscribers.id', '=', 'el2.subscriber_id' )
									->whereNull( 'el2.deleted_at' )
									->where( 'el2.list_id', $segment[ 'target_id' ] );
							}
							break;
					}
					$users     = $users->selectRaw( 'COUNT(DISTINCT users.email) as total_count' )->get();

					if( !empty( $lists ) )
						$subscribers = $subscribers->get();

					$duplicates = $users[ 0 ]->total_count + ( !empty( $lists ) ? $subscribers[ 0 ]->total_count : 0 );
				}
				else
				{
					$duplicates = 0;
				}

				switch( $segment['type'] )
				{
					case 'site':
						$sites[] = $segment['target_id'];
						break;
					case 'list':
						$lists[] = $segment['target_id'];
						break;
					case 'level':
						$levels[] = $segment['target_id'];
						break;
				}

				$return['segments'][ $segment['type'] . '_' . $segment['target_id'] ] = array(
					'duplicates' => ( !empty( $duplicates ) && $duplicates >= 0 ? $duplicates : 0 )
				);
			}

			$return['total'] = $total;
		}

		return $return;
	}

	public function inclusiveCalculateSubscribers()
	{
		$return = ['segments'=>[], 'total' => 0];
		if( \Input::has('segments') )
		{
			$total = 0;

			$sites = $lists = $levels = [];

			foreach( \Input::get('segments') as $segment )
			{
				$new_total = 0;

				switch( $segment['type'] )
				{
					case 'site':
						$sites[] = $segment['target_id'];
						break;
					case 'list':
						$lists[] = $segment['target_id'];
						break;
					case 'level':
						$levels[] = $segment['target_id'];
						break;
				}

				$users = new User();

				if( !empty( $sites ) )
				{
					$users = $users->leftjoin('roles as r', 'users.id', '=', 'r.user_id' );

					$users = $users->where(function($q) use ($sites){
						$q->whereIn('r.site_id', $sites );
						$q->whereNull('r.deleted_at');
					});
				}

				if( !empty( $levels ) )
				{
					$users = $users->leftjoin('access_passes as ap', 'users.id', '=', 'ap.user_id');
					if( !empty( $sites ) )
					{
						$users = $users->orwhere(function($q) use ($levels) {
							$q->whereIn('ap.access_level_id', $levels );
							$q->whereNull('ap.deleted_at');
						});
					}
					else
					{
						$users = $users->where(function($q) use ($levels) {
							$q->whereIn('ap.access_level_id', $levels );
							$q->whereNull('ap.deleted_at');
						});
					}
				}

				if( !empty( $lists ) )
				{
					$users = $users->leftjoin('email_subscribers as s', 'users.email', '=', 's.email' )
						->leftjoin('email_listledger as el', 's.id', '=', 'el.subscriber_id' );

					if( !empty( $sites ) || !empty( $levels ) )
					{
						$users = $users->orwhere( function ( $q ) use ( $lists )
						{
							$q->whereIn( 'el.list_id', $lists );
							$q->whereNull( 's.deleted_at' );
							$q->whereNull( 'el.deleted_at' );
						} );
					}
					else
					{
						$users = $users->where( function ( $q ) use ( $lists )
						{
							$q->whereIn( 'el.list_id', $lists );
							$q->whereNull( 's.deleted_at' );
							$q->whereNull( 'el.deleted_at' );
						} );
					}

					$subscribers = EmailSubscriber::leftjoin('users as u', 'email_subscribers.email', '=', 'u.email')
						->leftjoin('email_listledger as el', 'email_subscribers.id', '=', 'el.subscriber_id')
						->whereNull('u.email')
						->whereNull('el.deleted_at')
						->whereIn('el.list_id', $lists )
						->selectRaw( 'COUNT(DISTINCT email_subscribers.email) as total_count')
						->get();
				}

				$users = $users->selectRaw( 'COUNT(DISTINCT users.email) as total_count')->get();
				$new_total = $users[0]->total_count + ( !empty( $lists ) ? $subscribers[0]->total_count : 0 );

				$new_emails = $new_total - $total;
				$total = $new_total;

				$duplicates = $segment['count'] - $new_emails;

				$return['segments'][ $segment['type'] . '_' . $segment['target_id'] ] = array(
					'duplicates' => ( !empty( $duplicates ) && $duplicates >= 0 ? $duplicates : 0 )
				);
			}

			$return['total'] = $total;
		}

		return $return;
	}

	public function ListOfEmails()
	{
		$page_size = config("vars.default_page_size");
		$query = $this->model;
		$query = $query->orderBy('id' , 'DESC');
		$query = $query->whereNull('deleted_at');
		$query = $query->whereSiteId($this->site->id);

		$emails = $query->get();
		return $emails;
	}

    public function destroy($model){
        $model->removeQueue();
        return parent::destroy($model);
    }
}
