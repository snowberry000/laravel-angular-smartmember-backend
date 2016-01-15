<?php

namespace App\Models;
use App\Models\EmailList;

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

	public static function getHash($email) 
	{
		$hash = $email.'_'.microtime();
		$hash = md5( $hash );

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
		$subscriber->emailLists()->sync(array_keys($lists));

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
				\DB::table('email_listledger')->insert(['list_id' => $key, 'subscriber_id' => $existing_sub->id]);
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
		return array('record'=>$this,'total'=>$count);
	}


	public static function subscribersFromList($subscribers, $account_id, $list_id)
	{+
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
