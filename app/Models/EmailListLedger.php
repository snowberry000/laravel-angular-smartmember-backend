<?php namespace App\Models;

use Carbon\Carbon;
use App\Models\EmailAutoResponder;
use App\Models\EmailSubscriber;
use App\Models\EmailQueue;

class EmailListLedger extends Root
{
	protected $table = "email_listledger";

	public function getEmailHashAttribute( $value )
	{
		$email_hash = $value;

		if( empty( $value ) )
		{
			$subscriber = EmailSubscriber::find( $this->id );
			if( $subscriber )
			{
				$email_hash       = md5( trim( $subscriber->email ) );
				$subscriber->hash = $email_hash;
				$subscriber->save();
			}
		}

		return $email_hash;
	}

	public static function scheduleResponder($list_ledger)
	{
		//We search for autoresponder of the site
		//foreach autoresponder we search for the email
		//foreach email we check to see if there is queue item already

		$autoresponders = EmailAutoResponder::whereHas('emailLists', function($query) use ($list_ledger) {
			$query->where('list_id', $list_ledger->list_id);
		})->get();
		if ($autoresponders->count() > 0) {
			foreach ($autoresponders as $autoresponder) {
				$emails = $autoresponder->emails;
				$date = Carbon::parse($list_ledger->created_at);
				$subscriber = EmailSubscriber::find($list_ledger->subscriber_id);
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
						EmailQueue::enqueueAutoResponderEmail($email, $subscriber, '');
					}
				}
			}
		}

	}
}


EmailListLedger::created(function($list_ledger){
	\Log::info('email list ledger created');
	EmailListLedger::scheduleResponder($list_ledger);
});