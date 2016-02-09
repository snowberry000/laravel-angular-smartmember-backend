<?php namespace App\Models;


class EmailList extends Root
{
	protected $table = "email_lists";

	public function site() 
	{
	    return $this->belongsTo('App\Models\Site');
	}

	public function subscribers() 
	{
        return $this->belongsToMany('App\Models\EmailSubscriber', 'email_listledger', 'list_id', 'subscriber_id');
	}

	public static function createOrUpdate(array $data = array(), $model = null)
	{
		$subscriber_list = [];
		$subscribers = '';

		if (array_key_exists("segment_query", $data) && $data['segment_query'] != '')
		{
			$data['list_type'] = 'segment';
		}
		else if ( array_key_exists("subscribers", $data) )
		{
			$subscribers = $data['subscribers'];
			$data['list_type'] = 'user';
		}

		unset($data['subscribers']);
		
		if (! $model)
			$model = parent::create($data);
		else
			$model->fill($data);

		if ($subscribers)
			$subscriber_list = EmailSubscriber::subscribersFromList($subscribers, $data['account_id'], $model->id);

		if ( ! empty($subscriber_list) ) {
			foreach ($subscriber_list as $subscriber_id)
			{
				$email_list_ledger = new EmailListLedger();
				$email_list_ledger->list_id = $model->id;
				$email_list_ledger->subscriber_id = $subscriber_id;
				$email_list_ledger->save();
			}
			//$model->subscribers()->attach($subscriber_list);
            $model->total_subscribers = $model->total_subscribers + count($subscriber_list);
			$model->save();
		}

		if ($model->list_type == 'segment')
		{
			$segmentTool = new SegmentTool($model->segment_query);

			$model->total_subscribers = $segmentTool->getUsersCount();
			
			$model->save();
		}

		return $model;
	}

	public function applySearchQuery($q, $value)
	{
		return $q->where('name', 'like', '%' . $value . "%");	
	}

	public static function AddUnsubscribeToContent($content)
	{
		$api_url = \Config::get('app.url') . '/unsubcribe';

		// build manually because % gets messed up the http_build_query() way
		$api_url .= '?hash=@@@hash@@@&email_id=@@@email_id@@@&network_id=@@@network_id@@@&list_type=@@@list_type@@@';

		$text = 'To manage your email communication preferences, ';
		$text .= '<a href="' . $api_url . '">click here</a>.';

		$content = $content . "\n\n<p>" . $text . "</p>";

		return $content;
	}
}