<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\AppConfiguration\SendGridEmail;
use App\Models\User;
use App\Models\Site;
use App\Models\SiteMetaData;
use App\Models\EmailListLedger;
use App\Models\EmailSubscriber;
use App\Models\EmailRecipient;
use App\Models\EmailRecipientsQueue;
use App\Models\EmailJob;
use App\Models\UnsubscriberSegment;
use App\Models\Unsubscriber;
use Auth;

class EmailQueue extends Root
{
    protected $table = "emails_queue";

    public function site()
    {
        return $this->belongsTo('App\Models\Site');
    }

    public function subscriber()
    {
        return $this->hasOne('App\Models\EmailSubscriber', 'id', 'subscriber_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'subscriber_id');
    }

    public function email()
    {
        return $this->hasOne('App\Models\Email', 'id', 'email_id');
    }

	public function intro()
	{
		return $this->hasOne('App\Models\EmailRecipient', 'id', 'email_recipient_id');
	}

	public static function enqueueSegment($queue_item, $remaining)
	{
		$current_site = \Domain::getSite();

		$total_sent = 0;
		$segment = EmailRecipient::find( $queue_item->email_recipient_id );

		$more_remaining = false;

		if( $segment )
		{
			$segment_bits = explode( '_', $segment->recipient );
			switch( $segment_bits[0] )
			{
				case 'site':
					$new_emails = User::join('sites_roles as r', 'users.id', '=', 'r.user_id' )
						->where('r.site_id', $segment_bits[1])
						->whereNull('r.deleted_at')
						->select(['users.email','users.id', 'users.email_hash'])
						->take( $remaining + 1 )
						->distinct()
						->orderBy('id','ASC');

					if( $queue_item->last_recipient_queued )
						$new_emails = $new_emails->where('users.id','>', $queue_item->last_recipient_queued );

					$new_emails = $new_emails->get();
					break;
				case 'list':
					$new_emails = EmailListLedger::join('email_subscribers', 'email_subscribers.id','=','email_listledger.subscriber_id')
						->where( 'email_listledger.list_id', $segment_bits[1] )
						->whereNull('email_subscribers.deleted_at')
						->select(['email_subscribers.email','email_subscribers.id', 'email_subscribers.hash as email_hash'])
						->take( $remaining + 1 )
						->distinct()
						->orderBy('id','ASC');

					if( $queue_item->last_recipient_queued )
						$new_emails = $new_emails->where('email_subscribers.id','>', $queue_item->last_recipient_queued );

					$new_emails = $new_emails->get();
					break;
				case 'level':
					$new_emails = User::join('sites_roles as ap', 'users.id', '=', 'ap.user_id')
						->where('ap.access_level_id', $segment_bits[1])
						->whereNull('ap.deleted_at')
						->select(['users.email','users.id', 'users.email_hash'])
						->take( $remaining + 1 )
						->distinct()
						->orderBy('id','ASC');

					if( $queue_item->last_recipient_queued )
						$new_emails = $new_emails->where('users.id','>', $queue_item->last_recipient_queued );

					$new_emails = $new_emails->get();
					break;
				case 'catch':
					$user_site_ids = [ $current_site->id ];

					$site_ids = [ ];

					foreach( $user_site_ids as $site )
						$site_ids[] = $site;

					if( !$queue_item->info )
					{
						$new_emails = \App\Models\Site\Role::join( 'users', 'users.id', '=', 'sites_roles.user_id' )
							->whereNull( 'users.deleted_at' )
							->whereIn( 'sites_roles.site_id', $site_ids )
							->select( 'users.email' )
							->select( [ 'users.email', 'users.id', 'users.email_hash' ] )
							->take( $remaining + 1 )
							->distinct()
							->orderBy('id','ASC');

						if( $queue_item->last_recipient_queued )
							$new_emails = $new_emails->where( 'users.id', '>', $queue_item->last_recipient_queued );

						$new_emails = $new_emails->get();
					}
					else
					{
						$new_emails = EmailSubscriber::where( 'email_subscribers.account_id', $segment_bits[1] )
							->leftjoin('users','users.email','=','email_subscribers.email')
							->leftjoin('sites_roles',function($join) use ($site_ids) {
								$join->on('users.id','=','sites_roles.user_id');
								$join->whereIn('sites_roles.site_id',$site_ids);
							})
							->whereNull('users.email')
							->whereNull('users.deleted_at')
							->whereNull('sites_roles.deleted_at')
							->select(['email_subscribers.email','email_subscribers.id', 'email_subscribers.hash as email_hash'])
							->take( $remaining + 1 )
							->distinct()
							->orderBy('id','ASC');

						if( $queue_item->last_recipient_queued )
							$new_emails = $new_emails->where('email_subscribers.id','>', $queue_item->last_recipient_queued );

						$new_emails = $new_emails->get();
					}
					break;
			}

			if( !empty( $new_emails ) )
			{
				$counter = 0;
				$last_recipient_queued = 0;

				foreach( $new_emails as $key => $recipient )
				{
					$recipient->email_hash; //this makes it set the hash (via the attribute mutator) and save it if it didn't have one before
					$recipient->hash; //this makes it set the hash (via the attribute mutator) and save it if it didn't have one before

					$counter++;

					if( $counter > $remaining )
					{
						$queue_item->last_recipient_queued = $last_recipient_queued;
						$queue_item->save();
						$more_remaining = true;
						break;
					}
					elseif( $counter == count( $new_emails ) && $segment_bits[ 0 ] == 'catch' && !$queue_item->info )
					{
						$queue_item->last_recipient_queued = null;
						$queue_item->info = 'users_queued';
						$queue_item->save();
						$more_remaining = true;
					}

					$last_recipient_queued 		= $recipient->id;

					$already_queued = EmailQueue::whereEmailId( $segment->email_id );

					if( $segment->email_job_id )
					{
						$already_queued = $already_queued->withTrashed()->where(function($q) use ($segment) {
							$q->where(function($q2) use ($segment)
							{
								$q2->whereJobId( $segment->email_job_id );
							});
							$q->orwhere(function($q2){
								$q2->whereNull('deleted_at');
							});
						});
					}

					if( $segment_bits[ 0 ] == 'site' || $segment_bits[ 0 ] == 'level' || ( $segment_bits[ 0 ] == 'catch' && !$queue_item->info ) )
					{
						$subscriber = EmailSubscriber::join( 'users as u', 'u.email', '=', 'email_subscribers.email' )
							->where( 'u.id', $recipient->id )
							->select( 'email_subscribers.id' )
							->first();

						$already_queued = $already_queued->where( function ( $q2 ) use ( $recipient, $subscriber )
						{
							$q2->where( function ( $q ) use ( $recipient )
							{
								$q->whereListType( 'segment' );
								$q->whereSubscriberId( $recipient->id );
							} );
							if( $subscriber )
							{
								$q2->orwhere( function ( $q ) use ( $subscriber )
								{
									$q->where( function ( $query )
									{
										$query->whereNull( 'list_type' );
										$query->orwhere( 'list_type', '' );
									} );
									$q->whereSubscriberId( $subscriber->id );
								} );
							}
						} );
					}
					else
					{
						$user = EmailSubscriber::join( 'users as u', 'u.email', '=', 'email_subscribers.email' )
							->where( 'email_subscribers.id', $recipient->id )
							->select( 'u.id' )
							->first();

						$already_queued = $already_queued->where( function ( $q2 ) use ( $recipient, $user )
						{
							if( $user )
							{
								$q2->where( function ( $q ) use ( $user )
								{
									$q->whereListType( 'segment' );
									$q->whereSubscriberId( $user->id );
								} );
							}
							$q2->orwhere( function ( $q ) use ( $recipient )
							{
								$q->where( function ( $query )
								{
									$query->whereNull( 'list_type' );
									$query->orwhere( 'list_type', '' );
								} );
								$q->whereSubscriberId( $recipient->id );
							} );
						} );
					}

					$already_queued = $already_queued->first();

					if( $already_queued )
						continue;

					$tosend                     = [ ];
					$tosend[ 'site_id' ]     	= $queue_item->site_id;
					$tosend[ 'email_id' ]       = $segment->email_id;
					$tosend[ 'subscriber_id' ]  = $recipient->id;
					$tosend[ 'email_recipient_id' ] = $segment->id;
					$tosend[ 'list_type' ]      = ( $segment_bits[ 0 ] == 'site' || $segment_bits[ 0 ] == 'level' ) || ( $segment_bits[ 0 ] == 'catch' && !$queue_item->info ) ? 'segment' : null;
					$tosend[ 'job_id' ]         = $queue_item->email_job_id;
					$tosend[ 'send_at' ]        = isset( $queue_item->send_at ) ? $queue_item->send_at : Carbon::now();

					if( !self::checkIfUnsubscribed( $segment, $recipient, $tosend['list_type'], $queue_item->site_id ) )
						$queueEmails[] = $tosend;
				}
			}

			if( !empty( $queueEmails ) )
			{
				self::enqueueEmailsArray( $queueEmails );
				$total_sent = count( $queueEmails );
			}
		}

		if( !$more_remaining )
			$queue_item->delete();

		$remaining = $remaining - $total_sent;

		if( $remaining > 0 && $more_remaining )
			return self::enqueueSegment( $queue_item, $remaining );

		return $remaining;
	}

	/**
	 *
	 * @param $segment
	 * @param $recipient
	 * @param $type
	 * @return bool
	 */
	public static function checkIfUnsubscribed( $segment, $recipient, $type, $site_id )
	{
		switch( $type )
		{
			case 'segment':
				$unsubscribed = UnsubscriberSegment::whereEmail( $recipient->email )->whereSiteId( $site_id )->first();

				if( $unsubscribed )
					return true;
				break;
			default:
				//don't really need to check unsubscribed email subscribers, when they unsubscribed they are removed from the list they unsubscribed from.
		}

		return false;
	}

    public static function enqueueEmails($email = FALSE, $job_id = FALSE)
	{
		if( !$email )
			return;

		$site = \Domain::getSite();

		if( $job_id )
		{
			$email_job = EmailJob::find( $job_id );
			EmailQueue::where( 'job_id', $job_id )->forceDelete();
		}
		else
		{
			$email_job = new EmailJob;
		}

		$email_job->email_id   = $email->id;
		$email_job->site_id = $site->id;
		$email_job->send_at    = $email->send_at;
		$email_job->save();

		$recipients = [ ];

		$queueEmails = [ ];

		/*
		 * Create a new email job when emails get queued
		 */

		switch( $email->recipient_type )
		{
			case 'segment':
				foreach( $email->recipients as $segment )
				{
					if( $segment['type'] != 'segment' )
						continue;

					$queued_segment = new EmailRecipientsQueue;

					$queued_segment->site_id 				= $site->id;
					$queued_segment->email_recipient_id 	= $segment->id;
					$queued_segment->email_job_id 			= $email_job->id;
					$queued_segment->send_at 				= isset( $email_job->send_at ) ? $email_job->send_at : Carbon::now();
					$queued_segment->save();
				}
				break;
			case 'members':
				foreach( $email->recipients as $segment )
				{
					if( $segment[ 'type' ] != 'members' )
						continue;

					$recipient = User::whereEmail( $segment->recipient )->select(['email','id'])->first();

					$already_queued = EmailQueue::whereSubscriberId( $recipient->id )
										->whereListType('segment')
										->whereEmailId( $email->id )
										->first();

					if( $already_queued )
						continue;

					$tosend                    = [ ];
					$tosend[ 'site_id' ]	   = $email->site_id;
					$tosend[ 'email_id' ]      = $email->id;
					$tosend[ 'subscriber_id' ] = $recipient->id;
					$tosend[ 'list_type' ]     = 'segment';
					$tosend[ 'job_id' ]        = $email_job->id;
					$tosend[ 'send_at' ]       = isset( $email_job->send_at ) ? $email_job->send_at : Carbon::now();
					$duplicate_emails[]         = $recipient[ 'email' ];
					$queueEmails[]             = $tosend;
				}
				break;
			case 'single':
				foreach( $email->recipients as $segment )
				{
					if( $segment[ 'type' ] != 'single' )
						continue;

					$recipient = EmailSubscriber::whereEmail( $segment->recipient )->select( [ 'email', 'id' ] )->first();

					$site = Site::find( $email->site_id );

					if( !$recipient )
						$recipient = EmailSubscriber::create(['email'=>$segment->recipient,'account_id' => $site->user_id ]);

					$already_queued = EmailQueue::whereSubscriberId( $recipient->id )
						->where( function($q)
						{
							$q->whereNull( 'list_type' );
							$q->orwhere('list_type','');
						})
						->whereEmailId( $email->id )
						->first();

					if( $already_queued )
						continue;

					$tosend                    = [ ];
					$tosend[ 'site_id' ]       = $email->site_id;
					$tosend[ 'email_id' ]      = $email->id;
					$tosend[ 'subscriber_id' ] = $recipient->id;
					$tosend[ 'list_type' ]     = null;
					$tosend[ 'job_id' ]        = $email_job->id;
					$tosend[ 'send_at' ]       = isset( $email_job->send_at ) ? $email_job->send_at : Carbon::now();
					$duplicate_emails[]        = $recipient[ 'email' ];
					$queueEmails[]             = $tosend;
				}
				break;
		}

		if( !empty( $queueEmails ) )
			self::enqueueEmailsArray( $queueEmails );
    }

	public static function enqueueEmailsArray( $queueEmails )
	{
		if( !empty( $queueEmails ) )
		{
			$length           = count( $queueEmails );
			$max_placeholders = 65535;

			$cols = array_keys( $queueEmails[ 0 ] );

			foreach( $cols as $key => $val )
				$cols[ $key ] = '`' . $val . '`';

			$column_count = count( $cols );

			$max_rows_per_insert = floor( $max_placeholders / $column_count );

			for( $x = 0; $x < $length; $x = $x + $max_rows_per_insert )
			{
				$insert_recipients = array_slice( $queueEmails, $x, $max_rows_per_insert );

				$insert_values = [ ];

				foreach( $insert_recipients as $recipient )
				{
					foreach( $recipient as $key => $val )
						$recipient[ $key ] = "'" . $val . "'";

					$insert_values[] = '(' . implode( ',', $recipient ) . ')';
				}

				$sql = "INSERT DELAYED INTO `emails_queue` (" . implode( ',', $cols ) . ') values ' . implode( ',', $insert_values ) . ';';
				\DB::statement( $sql );
			}
		}
	}

	public static function enqueueAutoResponderEmail($email, $subscriber)
    {
        if (!$email) return;

        $queueEmails = [];
        
        $tosend = [];
        $tosend['site_id'] = $email->site_id;
        $tosend['email_id'] = $email->id;
        $tosend['subscriber_id'] = $subscriber->id;
        $tosend['list_type'] = ! empty( $subscriber->list_type ) ? $subscriber->list_type : null;
        $tosend['send_at'] = isset($email->send_at) ? $email->send_at : Carbon::now();
        $queueEmails[] = $tosend;

		$length = count( $queueEmails );
		$max_placeholders = 65535;
		$columns = 5;

		$max_rows_per_insert = floor( $max_placeholders / $columns );

		for( $x = 0; $x < $queueEmails; $x = $x + $max_rows_per_insert + 1 )
		{
			EmailQueue::insert( array_slice( $queueEmails, $x, $max_rows_per_insert ) );
		}
    }

    function current_time($type, $gmt = 0)
    {
        switch ($type) {
            case 'mysql':
                return ($gmt) ? gmdate('Y-m-d H:i:s') : gmdate('Y-m-d H:i:s', (time() + (get_option('gmt_offset') * HOUR_IN_SECONDS)));
            case 'timestamp':
                return ($gmt) ? time() : time() + (get_option('gmt_offset') * HOUR_IN_SECONDS);
            default:
                return ($gmt) ? date($type) : date($type, time() + (get_option('gmt_offset') * HOUR_IN_SECONDS));
        }
    }

    function time()
    {
        return time();
    }

    function lockQueue($site_id)
    {
		$now = Carbon::now();
		SiteMetaData::create(['site_id' => $site_id, 'key' => 'email_queue_locked', 'value' => $now->timestamp + 300 ]);
    }

    function unLockQueue($site_id)
    {
		$email_queue_locked = SiteMetaData::whereSiteId($site_id)->whereKey('email_queue_locked')->get();

		foreach( $email_queue_locked as $lock_item )
			$lock_item->forceDelete();
    }

    function isQueueLocked($site_id)
    {
		$now = Carbon::now();
		$email_queue_locked = SiteMetaData::whereSiteId($site_id)->whereKey('email_queue_locked')->first();

		if (isset($email_queue_locked) && $email_queue_locked->value > $now->timestamp) {
			return true;
		}

		return false;
    }

	function IsRecipientQueueLocked($site_id)
    {
        $now = Carbon::now();
        $email_queue_locked = SiteMetaData::whereSiteId($site_id)->whereKey('email_recipient_queue_locked')->first();

        if (isset($email_queue_locked) && $email_queue_locked->value > $now->timestamp) {
            return true;
        }

        return false;
    }

	function lockRecipientQueue($site_id)
	{
		$now = Carbon::now();
		SiteMetaData::create(['site_id' => $site_id, 'key' => 'email_recipient_queue_locked', 'value' => $now->timestamp + 300 ]);
	}

	function unLockRecipientQueue($site_id)
	{
		$email_queue_locked = SiteMetaData::whereSiteId($site_id)->whereKey('email_recipient_queue_locked')->first();

		if( $email_queue_locked )
			$email_queue_locked->delete();
	}

    function injectTrackingIntoContent($content, $site_id, $email_id = '', $job_id = '', $subscriber_id = '', $do_click_tracking = true)
    {
        if ($do_click_tracking)
            $content = Link::EncodeLinksInContent($content, $job_id);

        $content = Email::AddSignatureToContent($content, $email_id, $site_id);
        $content = Email::AddUnsubscribeToContent( $content, $site_id );
        $content = Open::AddPixelToContent($content);

        return $content;
    }

    public function processQueue($site_id, $abort_on_lock = true)
    {   
        if ($this->IsQueueLocked($site_id)) {
            if ($abort_on_lock)
                \App::abort(403, "The queue is locked right now. Please try again later");
            else return;
        }

        $this->lockQueue($site_id);
        $data = $this->queueHelper($site_id);
        $this->unLockQueue($site_id);

        return $data;
    }

	public function processRecipientsQueue($site_id, $abort_on_lock = true)
    {
        if ($this->IsRecipientQueueLocked($site_id)) {
            if ($abort_on_lock)
                \App::abort(403, "The queue is locked right now. Please try again later");
            else return;
        }

        $this->lockRecipientQueue($site_id);
        $data = $this->recipientQueueHelper($site_id);
        $this->unLockRecipientQueue($site_id);

        return $data;
    }

	private function recipientQueueHelper( $site_id )
	{
		$per_run = 4000;
		$remaining = 4000;

		$queue_items = EmailRecipientsQueue::whereSiteId($site_id)->skip(0)->take($per_run)->get();

		foreach( $queue_items as $queue_item )
		{
			$remaining = self::enqueueSegment( $queue_item, $remaining );

			if( $remaining < 1 )
				break;
		}

		return array( 'total_queued' => $per_run - $remaining );
	}

    private function queueHelper($site_id)
    {
		$now = Carbon::now();

		$site_meta = SiteMetaData::whereSiteId( $site_id )->whereKey('last_email_sent')->first();

		if( !$site_meta )
			$site_meta = SiteMetaData::create(['site_id' => $site_id, 'key' => 'last_email_sent' ]);

		$site_meta->value = $now;
		$site_meta->save();

        $per_run = 4000;
        $queue_items = EmailQueue::whereSiteId($site_id)->where('send_at', '<', Carbon::now())->skip(0)->take($per_run)->get();
        $debug = EmailQueue::whereSiteId($site_id)->where('send_at', '<', Carbon::now())->skip(0)->take($per_run)->toSql();
        \Log::info($debug);
        \Log::info(Carbon::now('America/Chicago'));
        $emails_already_pulled = array();
        $intros_already_pulled = array();

        $emails = array();
        $substitutions = array();
		$custom_intros = array();

        foreach ($queue_items as $queue_item) 
        {
			preg_match_all("|%[a-zA-Z0-9-_]+%|U", $queue_item->email->content, $matches, PREG_PATTERN_ORDER);

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
			}

            if( $queue_item->list_type == 'segment' && !isset($queue_item->user->email)) 
                continue;

            if ($queue_item->list_type != 'segment' && !isset($queue_item->subscriber->email))
                continue;

            if( $queue_item->list_type == 'segment')
                $emails[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro'][$queue_item->user->email] = $queue_item->id;
            else
                $emails[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro'][$queue_item->subscriber->email] = $queue_item->id;

            $queue[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro'][] = $queue_item;
            $emails[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['site_id'] = $site_id;

            if( $queue_item->list_type == 'segment')
            {
                $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%hash%'][] =  $queue_item->user->email_hash;
                $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%subscriber_id%'][] = $queue_item->user->id;
                $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%subscriber_name%'][] = $queue_item->user->first_name;
                $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%list_type%'][] = "segment";

				if( !empty( $additional_meta ) )
				{
					$meta_data = [];

					$meta_data_raw = $queue_item->user->meta( $queue_item->site_id )->get();

					if( !( $meta_data_raw->isEmpty() ) )
					{
						foreach( $meta_data_raw as $meta_key => $meta_val )
						{
							$meta_data[ '%' . $meta_val->key . '%' ] = $meta_val->value;
						}
					}

					foreach( $additional_meta as $item )
					{
						if( $item == '%aid%' && (empty( $meta_data[ $item ] ) || !isset($meta_data[$item]) ))
						{
							$substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro'][ $item ][] = 0;
						}
						else
						{
							$substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro'][ $item ][] = !empty( $meta_data[ $item ] ) ? $meta_data[ $item ] : '';
						}
					}
				}
            }
            else
            {
                $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%hash%'][] =  $queue_item->subscriber->hash;
                $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%subscriber_id%'][] = $queue_item->subscriber->id;
                $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%subscriber_name%'][] = $queue_item->subscriber->name;
                $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%list_type%'][] = "user";

				if( !empty( $additional_meta ) )
				{
					$meta_data = [];

					$subscriber_user = $queue_item->subscriber->user;

					if( !empty( $subscriber_user ) )
					{
						$meta_data_raw = $queue_item->subscriber->user->meta( $queue_item->site_id )->get();

						if( !( $meta_data_raw->isEmpty() ) )
						{
							foreach( $meta_data_raw as $meta_key => $meta_val )
							{
								$meta_data[ '%' . $meta_val->key . '%' ] = $meta_val->value;
							}
						}
					}

					foreach( $additional_meta as $item )
					{
						if( $item == '%aid%' && (empty( $meta_data[ $item ] ) || !isset($meta_data[$item]) ))
						{
							$substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro'][ $item ][] = 0;
						}
						else
						{
							$substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro'][ $item ][] = !empty( $meta_data[ $item ] ) ? $meta_data[ $item ] : '';
						}
					}
				}
            }


            $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%email_id%'][] = $queue_item->email_id;
            $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%job_id%'][] = $queue_item->job_id;
            $substitutions[$queue_item->email_id][ $queue_item->email_recipient_id ? $queue_item->email_recipient_id : 'no_intro']['%network_id%'][] = $site_id;
        }

        $total_email_sent = 0;

        foreach ($emails as $key => $value)
		{
			foreach( $value as $key2 => $value2 )
			{
				if( !isset( $emails_already_pulled[ $key ] ) )
				{
					$email = Email::with( 'recipients' )->whereId( $key )->first();

					if( !$email )
						continue;

					if( $email->recipients )
					{
						foreach( $email->recipients as $key3 => $intro )
						{
							if( empty( $intros_already_pulled[ $intro->id ] ) )
								$intros_already_pulled[ $intro->id ] = $intro;
						}
					}

					$emails_already_pulled[ $key ] = $email;

					$email->content = $this->injectTrackingIntoContent( $email->content, $site_id,
																		$email->id, $queue[ $key ][ $key2 ][ 0 ][ 'job_id' ], $subscriber_id = '', $do_click_tracking = false );
				}

				$intro = !empty( $intros_already_pulled[ $key2 ] ) ? $intros_already_pulled[ $key2 ] : [];

				$sending_email             = new Email();
				$sending_email->site_id    = $value2[ 'site_id' ];
				unset( $value2[ 'site_id' ] );

				$to                                  = array_keys( $value2 );
				$to_ids                              = array_values( $value2 );
				$sending_email->to                   = $to;
				$sending_email->subject              = !empty( $intro->subject ) ? $intro->subject : $email->subject;
				$sending_email->content              = ( !empty( $intro->intro ) ? $intro->intro : '' ) . $email->content;
				$sending_email->id                   = $email->id;
				$sending_email->original_email		 = $email;
				$sending_email->sendgrid_integration = $email->sendgrid_integration;
				$sending_email->substitutions        = $substitutions[ $key ][ $key2 ];

				$result = SendGridEmail::processEmailQueue( $sending_email );
				if( isset( $result ) && is_object( $result ) )
				{
					$total_email_sent += count( $to_ids );
					foreach( $to_ids as $to_id )
					{
						EmailQueue::whereId( $to_id )->delete();
					}
					foreach( $queue[ $key ][ $key2 ] as $key3 => $value3 )
					{
						$fields                    = array();
						$fields[ 'subscriber_id' ] = $value3[ 'subscriber_id' ];
						$fields[ 'email_id' ]      = $key2;
						$fields[ 'list_type' ]     = $value3[ 'list_type' ];
						$fields[ 'job_id' ]        = $value3[ 'job_id' ];
						EmailHistory::insert( $fields );
					}
				}
				else
				{
					\App::abort( 403, "There is something wrong with our email system. Please email support and check back later" );
				}
			}
        }
        return array('data' => EmailQueue::whereSiteId($site_id)->skip(0)->take($per_run)->get(), 'last_email_sent' => $now->toDateTimeString(), 'total_email_sent' => $total_email_sent);
    }

}
