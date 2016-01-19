<?php namespace App\Models;


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
}