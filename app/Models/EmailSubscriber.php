<?php

namespace App\Models;
use App\Models\EmailList;
use App\Models\EmailListLedger;

class EmailSubscriber extends Root
{
	protected $table = "email_subscribers";
	// protected $with = "emailLists";
	protected $fillable = ['name', 'email', 'site_id', 'hash'];

	public function site()
	{
	    return $this->belongsTo('App\Models\Site');
	}

	public function emailLists()
	{
		return $this->belongsToMany('App\Models\EmailList', 'email_listledger', 'subscriber_id', 'list_id')->withPivot('created_at');
	}

	public function user()
	{
		return $this->hasOne('App\Models\User', 'email', 'email');
	}

	public function applySearchQuery($q, $value)
	{
		return $q->where(function($query) use ($value) {
			$query->where('email', 'like', '%' . $value . '%');
			$query->orwhere('name', 'like', '%' . $value . '%');
		});
	}

	public function scopeWithAndWhereHas($query, $relation, $constraint){
		return $query->whereHas($relation, $constraint)
				->with([$relation => $constraint]);
	}

	public static function getHash($email) 
	{
		$hash = $email.'_'.microtime();
		$hash = md5( $hash );

		return $hash;
	}

	public function getHashAttribute( $value )
	{
		if( empty( $value ) )
		{
			$hash = md5(trim($this->email));
			$this->hash = $hash;
			$this->save();
		}
		else
		{
			$hash = $value;
		}
		return $hash;
	}

	public static function create(array $data = array())
	{
		$lists = [];
		if (array_key_exists('lists', $data))
		{	
			$lists = $data['lists'];
			unset($data['lists']);
		}
		$subscriber = parent::create($data);
		$subscriber->account_id = $data['account_id'];
		$subscriber->save();
		foreach (array_keys($lists) as $list_id)
		{
			$email_list_ledger = new EmailListLedger();
			$email_list_ledger->list_id = $list_id;
			$email_list_ledger->subscriber_id = $subscriber->id;
			$email_list_ledger->save();
		}
		//$subscriber->emailLists()->sync(array_keys($lists));

		return $subscriber;
	}

	public function update(array $data = array())
	{
		$lists = [];
		$count = 0;
		if (array_key_exists('lists', $data))
		{
			$lists = $data['lists'];
			unset($data['lists']);
		}

		$existing_sub = EmailSubscriber::find($data['id']);
		if (isset($data['name']))
		{
			$existing_sub->name = $data[ 'name' ];
			$existing_sub->save();
		}

		foreach ($lists as $key => $value)
		{
			$record = \DB::table('email_listledger')->where('list_id', '=', $key)->where('subscriber_id', '=', $existing_sub->id)->first();
			if (isset($record))
				unset($lists[$key]);
			else
			{
				$count++;
				$email_list_ledger = new EmailListLedger();
				$email_list_ledger->list_id = $key;
				$email_list_ledger->subscriber_id = $existing_sub->id;
				$email_list_ledger->save();
				//\DB::table('email_listledger')->insert(['list_id' => $key, 'subscriber_id' => $existing_sub->id]);
			}
		}
		return array('record'=>$this,'total'=>$count);
	}


	public static function subscribersFromList($subscribers, $account_id, $list_id)
	{
		$bits = preg_split('/[\ \n\,]+/', $subscribers );
		$subscriber_list = [];
		
		if( $bits )
		{
			$import_count = 0;

			foreach( $bits as $key => $value )
			{
				$value = trim( $value );

				if( !$value )
					continue;

				$import_count++;
				$fields = array();

				//Handle Creation ($email,  $email_lists)
				$subscriber = EmailSubscriber::firstOrNew(['email' => $value, 'account_id' => $account_id]);
				$subscriber->hash = EmailSubscriber::getHash($value);

				if (!$subscriber->id) 
				{
					$subscriber->name = $value;
					$subscriber->account_id = $account_id;
					$subscriber->save();
					$subscriber_list[] = $subscriber->id;
				}
				else
				{
					$alreadyThere = $subscriber->emailLists()->where('list_id', $list_id)->first();
					
					if (! $alreadyThere)
						$subscriber_list[] = $subscriber->id;
				}
			}
		}

		return $subscriber_list;
	}
}

EmailSubscriber::saving(function($model){
	preg_match( '/[a-z0-9\.\_\-\+]{1,}@[a-z0-9\-]{2,}\.[a-z0-9\-\.]{2,}/i', $model->email, $matches );

	if( !empty( $matches ) && !empty( $matches[0] ) )
		$model->email = trim( $matches[0] );

	$model->name = trim( $model->name );
});

EmailSubscriber::saved(function($model){
	preg_match( '/[a-z0-9\.\_\-\+]{1,}@[a-z0-9\-]{2,}\.[a-z0-9\-\.]{2,}/i', $model->email, $matches );

	if( empty( $matches ) || empty( $matches[0] ) )
		$model->delete();
});
