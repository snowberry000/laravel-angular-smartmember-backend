<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\SegmentTool;
use App\Models\EmailListLedger;


class EmailAutoResponder extends Root
{
	protected $table = "email_autoresponder";

	public function emails()
	{
	    return $this->belongsToMany('App\Models\Email', 'email_autoresponder_email', 'autoresponder_id', 'email_id')->withPivot('delay', 'unit','sort_order')->orderBy('pivot_sort_order');
	}

	public function emailLists() 
	{
		return $this->belongsToMany('App\Models\EmailList', 'email_autoresponder_list', 'autoresponder_id', 'list_id');
	}

	public function applySearchQuery($q, $value)
	{
	}

	public static function create(array $data = array())
	{
		$emails = [];
		if (array_key_exists('emails', $data))
		{
			$emails = $data['emails'];
			unset($data['emails']);
		}

		$lists = [];
		if (array_key_exists('lists', $data))
		{
			$lists = $data['lists'];
			unset($data['lists']);
		}

		$data['processed_at'] = \Carbon\Carbon::now();
		$responder = parent::create($data);

		$tmp = [];
		foreach ($emails as $email)
		{
			unset($email['subject']);
			$tmp[] = $email;

		}
		$emails = $tmp;
		
		$responder->emails()->attach($emails);

		// Set email lists
		$ids = array_keys($lists);
		$responder->emailLists()->sync($ids);

		return $responder;
	}

	public static function processAutoResponders()
	{
		$responders = self::with(['emails', 'emailLists'])->get();

		foreach ($responders as $responder)
		{
			static::processAutoResponder($responder);
			$responder->processed_at = Carbon::now();
			$responder->save();
		}
	}

	public static function processAutoResponder($responder)
	{
		static::autoResponderUserList($responder);
		//static::autResponderSegmentList($responder);
	}

	private static function autoResponderUserList($responder)
	{
		$userLists = $responder->emailLists()->where('list_type', 'user')->get();

		$ids = array_pluck($userLists, "id");
		$subscribers = EmailSubscriber::with('emailLists')
						->whereHas('emailLists', function($q) use ($ids) {
							$q->whereIn('email_lists.id', $ids);
						})->get();


		$emails = $responder->emails;
		foreach ($subscribers as $subscriber)
		{
			$subscription = EmailListLedger::whereSubscriberId( $subscriber->id )->whereIn('list_id', $ids)->where('created_at','>',$responder->processed_at)->orderBy('created_at','DESC')->first();

			if( !$subscription )
				continue;
			
			// Starting date based on the date of subscribing
			$date = Carbon::parse( $subscription->created_at );
			foreach ($emails as $email)
			{
				switch ($email->pivot->unit)
				{
					case 1:
						$date = $date->addHours($email->pivot->delay);
						break;
					case 2:
						$date = $date->addDays($email->pivot->delay);
						break;
					case 3:
						$date = $date->addMonths($email->pivot->delay);
						break;

				}

				$email->send_at = $date;
				EmailQueue::enqueueAutoResponderEmail($email, $subscriber);
			}

		}
	}

	private static function autResponderSegmentList($responder)
	{
		$segmentLists = $responder->emailLists()->where('list_type', 'segment')->get();

		$sites = [ $responder->site_id ];

		$emails = $responder->emails;
		foreach ($segmentLists as $segmentList)
		{
			$segTool = new SegmentTool($segmentList->segment_query, $sites);
			$users = $segTool->getUsers();

			foreach ($users as $user => $created_at)
			{
				// Starting date based on the date/time the user was made part of that list. 
				$date = $created_at;
				$subscriber = User::whereEmail( $user )->first();
				$subscriber->list_type = 'segment';

				foreach ($emails as $email)
				{
					switch ($email->pivot->unit)
					{
						case 1:
							$date = $date->addHours($email->pivot->delay);
							break;
						case 2:
							$date = $date->addDays($email->pivot->delay);
							break;
						case 3:
							$date = $date->addMonths($email->pivot->delay);
							break;

					}

					$email->send_at = $date;
					EmailQueue::enqueueAutoResponderEmail($email, $subscriber);
				}	
			}
		}
	}
}
