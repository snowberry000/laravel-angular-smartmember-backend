<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\SegmentTool;
use App\Models\EmailListLedger;
use App\Models\AccessLevel;
use App\Models\Site;
use App\Models\Site\Role;
use App\Models\EmailQueue;


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

	public function accessLevels()
	{
		return $this->belongsToMany('App\Models\AccessLevel', 'email_autoresponder_access_level', 'autoresponder_id', 'access_level_id');
	}

	public function sites()
	{
		return $this->belongsToMany('App\Models\Site', 'email_autoresponder_site', 'autoresponder_id', 'site_id');
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
		$access_levels = [];
		$sites = [];
		if (array_key_exists('post_lists', $data))
		{
			$lists = $data['post_lists'];
			unset($data['post_lists']);
		}
		if (array_key_exists('post_access_levels', $data))
		{
			$access_levels = $data['post_access_levels'];
			unset($data['post_access_levels']);
		}
		if (array_key_exists('post_sites', $data))
		{
			$sites = $data['post_sites'];
			unset($data['post_sites']);
		}
		$data['processed_at'] = null;
		$responder = parent::create($data);

		$tmp = [];
		foreach ($emails as $email)
		{
			unset($email['subject']);
			$tmp[] = $email;

		}
		$emails = $tmp;
		
		$responder->emails()->attach($emails);

		$responder->emailLists()->sync($lists);
		$responder->accessLevels()->sync($access_levels);
		$responder->sites()->sync($sites);
		return $responder;
	}

	public static function processAutoResponders()
	{
		$responders = self::with(['emails', 'emailLists'])->whereNull('processed_at')->get();

		foreach ($responders as $responder)
		{
			$responder->processed_at = Carbon::now();
			$responder->save();
			\Log::info('process auto responder' . $responder->id);
			static::processAutoResponder($responder);
			\Log::info('finish enqueing');
		}
	}

	public static function processAutoResponder($responder)
	{
		switch ($responder->email_when)
		{
			case 1:
				static::autoResponderMemberList($responder);
				break;
			case 2:
				static::autoResponderBuyerList($responder);
				break;
			case 3:
				static::autoResponderUserList($responder);
				break;

		}
		//static::autResponderSegmentList($responder);
	}


	private static function autoResponderBuyerList($responder)
	{
		$access_levels = $responder->accessLevels()->select('accessLevels.id')->lists('accessLevels.id')->toArray();
		$users = Role::with('user')->whereIn('access_level_id', $access_levels)->whereType('member')->distinct()->get();

		$emails = $responder->emails;
		foreach ($users as $subscriber)
		{
			$date = Carbon::parse($subscriber->created_at );
			foreach ($emails as $email)
			{
				switch ($email->pivot->unit)
				{
					case 1:
						if ($email->pivot->delay == 0 || $email->pivot->delay == '0')
							$date = $date->addMinutes(5);
						else
							$date = $date->addHours($email->pivot->delay);
						break;
					case 2:
						$date = $date->addDays($email->pivot->delay);
						break;
					case 3:
						$date = $date->addMonths($email->pivot->delay);
						break;
				}
				if ($date->timestamp > Carbon::now()->timestamp)
				{
					$email->send_at = $date;
					EmailQueue::enqueueAutoResponderEmail($email, $subscriber, 'segment');
				}
			}
		}
	}

	private static function autoResponderMemberList($responder)
	{
		$sites = $responder->sites()->select('sites.id')->lists('sites.id')->toArray();
		\Log::info($sites);
		$users = Role::with('user')->whereIn('site_id', $sites)->whereType('member')->distinct()->get();

		$emails = $responder->emails;
		foreach ($users as $subscriber)
		{
			$date = Carbon::parse($subscriber->created_at );
			foreach ($emails as $email)
			{
				switch ($email->pivot->unit)
				{
					case 1:
						if ($email->pivot->delay == 0 || $email->pivot->delay == '0')
							$date = $date->addMinutes(5);
						else
							$date = $date->addHours($email->pivot->delay);
						break;
					case 2:
						$date = $date->addDays($email->pivot->delay);
						break;
					case 3:
						$date = $date->addMonths($email->pivot->delay);
						break;
				}
				if ($date->timestamp > Carbon::now()->timestamp)
				{
					$email->send_at = $date;
					EmailQueue::enqueueAutoResponderEmail($email, $subscriber, 'segment');
				}
			}
		}
	}


	private static function autoResponderUserList($responder)
	{
		$userLists = $responder->emailLists()->where('list_type', 'user')->get();

		$ids = array_pluck($userLists, "id");
		$subscribers = EmailSubscriber::with('emailLists')
			->whereHas('emailLists', function ($q) use ($ids) {
				$q->whereIn('email_lists.id', $ids);
			})->get();


		$emails = $responder->emails;
		foreach ($subscribers as $subscriber) {
			$subscription = EmailListLedger::whereSubscriberId($subscriber->id)->whereIn('list_id', $ids)->where('created_at', '>', $responder->processed_at)->orderBy('created_at', 'DESC')->first();

			if (!$subscription)
				continue;

			// Starting date based on the date of subscribing
			$date = Carbon::parse($subscription->created_at);
			foreach ($emails as $email) {
				switch ($email->pivot->unit) {
					case 1:
						if ($email->pivot->delay == 0 || $email->pivot->delay == '0')
							$date = $date->addMinutes(5);
						else
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

		$sites = [$responder->site_id];

		$emails = $responder->emails;
		foreach ($segmentLists as $segmentList) {
			$segTool = new SegmentTool($segmentList->segment_query, $sites);
			$users = $segTool->getUsers();

			foreach ($users as $user => $created_at) {
				// Starting date based on the date/time the user was made part of that list. 
				$date = $created_at;
				$subscriber = User::whereEmail($user)->first();
				$subscriber->list_type = 'segment';

				foreach ($emails as $email) {
					switch ($email->pivot->unit) {
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
