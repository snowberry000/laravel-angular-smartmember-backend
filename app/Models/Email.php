<?php

namespace App\Models;

use App\Models\AppConfiguration\SendGridEmail;
use Carbon\Carbon;
use Input;
use App\Models\EmailRecipient;

class Email extends Root
{
	protected $table = "emails";

	public static $EMAIL_SAVE = 1;
	public static $EMAIL_PREVIEW = 2;
	public static $EMAIL_SEND = 3;

	public function site()
	{
	    return $this->belongsTo('App\Models\Site');
	}

	public function recipientLists() 
	{
		return $this->belongsToMany('App\Models\EmailList', 'email_recipient', 'email_id', 'list_id');
	}

	public function links()
	{
		return $this->hasMany('App\Models\Link', 'email_id');
	}

    public function opens()
    {
        return $this->hasMany('App\Models\Open', 'email_id');
    }

	public function recipients()
	{
		return $this->hasMany('App\Models\EmailRecipient', 'email_id')->orderBy('order','ASC');
	}

	public function email_histories()
	{
		return $this->hasMany('App\Models\EmailHistory', 'email_id');
	}

	public function email_jobs()
	{
		return $this->hasMany('App\Models\EmailJob', 'email_id');
	}

	public function applySearchQuery($q, $value)
	{
	
		return $q->where('subject', 'like','%' . $value . "%");
	}

	public function processAction(array $data = array())
	{
		$action = isset($data['action']) ? $data['action'] : self::$EMAIL_SAVE;

		if ($action == self::$EMAIL_SEND) 
		{
			$this->send_at = isset($data['send_at']) ? $data['send_at'] : Carbon::now();
			$job_id = isset($data['job_id']) ? $data['job_id'] : FALSE;

			EmailQueue::enqueueEmails($this, $job_id);
		}
	}

	public function saveLists(array $data = array())
	{
		$list_ids = [];

		if ( ! isset ($data['emailLists']))
			return $list_ids;

		$email_lists = $data['emailLists'];

		foreach ($email_lists as $key => $checked)
		{
			if ($checked)
				$list_ids[] = $key;
		}

        if (isset($list_ids)) {
            $this->recipientLists()->sync($list_ids);
        }
	}

	public static function AddUnsubscribeToContent($content, $site_id)
	{
		$site = Site::find( $site_id );
        $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'http://my.smartmember.com';
        $parts = explode(".", $domain);
        $tld = array_pop($parts);
        $rootDomain = array_pop($parts) . "." . $tld;
        if (strpos($domain, "smartmember") === false){
            $rootDomain = "smartmember.com";
        }

		$app_url = 'http://' . $site->subdomain . '.' . $rootDomain . '/sign/unsubscribe';

		// build manually because % gets messed up the http_build_query() way
		$app_url .= '?hash=@@@hash@@@&job_id=@@@job_id@@@&network_id=@@@network_id@@@&list_type=@@@list_type@@@&segment_id=@@@segment_id@@@';

		$text = 'To manage your email communication preferences, ';
		$text .= '<a href="' . $app_url . '">click here</a>.';

		$content = $content . "\n\n<p>" . $text . "</p>";

		return $content;
	}

	public static function AddSignatureToContent ($content, $email_id, $site_id) {

		$email = Email::find($email_id);

		$email_signature = $email->mail_signature;
		if ($email_signature && $email_signature != "")
		{
			$content = $content . "\n\n<p>" . $email_signature . "</p>";
		}
		else
		{
			$email_setting = EmailSetting::whereSiteId($site_id)->first();
			$email_signature = isset($email_setting->email_signature) ? $email_setting->email_signature : '';
			if ($email_signature && $email_signature != "")
				$content = $content . "\n\n<p>" . $email_signature . "</p>";
		}
		return $content;
	}

	public function removeQueue(){
		return EmailQueue::whereEmailId($this->id)->delete();
	}
}

Email::saved(function($model){
	$intros = [];
	$order = 1;
	if( !empty( $model->recipient_type ) )
	{
		switch( $model->recipient_type )
		{
			case 'segment':
				if( \Input::has('intros') )
				{
					foreach( \Input::get( 'intros' ) as $intro_data )
					{
						if( !empty( $intro_data[ 'id' ] ) )
							$intro = EmailRecipient::find( $intro_data[ 'id' ] );
						else
							$intro = EmailRecipient::create( [ 'type' => 'segment' ] );

						if( empty( $intro ) )
						{
							//just in case the intro had an id but was deleted
							$intro = EmailRecipient::create( [ 'type' => 'segment' ] );
						}

						$intro->type      = 'segment';
						$intro->recipient = $intro_data[ 'type' ] . '_' . $intro_data[ 'target_id' ];
						$intro->order     = $order;
						$intro->subject   = !empty( $intro_data[ 'subject' ] ) ? $intro_data[ 'subject' ] : '';
						$intro->intro     = !empty( $intro_data[ 'intro' ] ) ? $intro_data[ 'intro' ] : '';
						$intro->email_id  = $model->id;
						$intro->save();

						$intros[] = $intro->id;

						$order++;
					}

					$extra_recipients = EmailRecipient::whereNotIn( 'id', $intros )->whereType('segment')->whereEmailId( $model->id )->get();

					foreach( $extra_recipients as $extra_recipient )
						$extra_recipient->delete();
				}
				break;
			case 'members':
				if( \Input::has('recipients') )
				{
					foreach( \Input::get( 'recipients' ) as $recipient )
					{
						$intro = EmailRecipient::whereEmailId( $model->id )->whereType('members')->whereRecipient( $recipient )->first();

						if( !$intro )
							$intro = EmailRecipient::create( ['type' => 'members' ] );

						$intro->type      = 'members';
						$intro->recipient = $recipient;
						$intro->order     = $order;
						$intro->email_id  = $model->id;
						$intro->save();

						$intros[] = $intro->id;

						$order++;
					}

					$extra_recipients = EmailRecipient::whereNotIn( 'id', $intros )->whereType('members')->whereEmailId( $model->id )->get();

					foreach( $extra_recipients as $extra_recipient )
						$extra_recipient->delete();
				}
				break;
			case 'single':
				if( \Input::has('recipient') )
				{
					$intro = EmailRecipient::whereEmailId( $model->id )->whereType('single')->first();

					if( !$intro )
						$intro = EmailRecipient::create( ['type' => 'single' ] );

					$intro->type      = 'single';
					$intro->recipient = \Input::get( 'recipient' );
					$intro->order     = 1;
					$intro->email_id  = $model->id;
					$intro->save();

					$extra_recipients = EmailRecipient::where( 'id', '!=', $intro->id )->whereType('single')->whereEmailId( $model->id )->get();

					foreach( $extra_recipients as $extra_recipient )
						$extra_recipient->delete();
				}
				break;
		}
	}
});
