<?php

namespace App\Models;

class EmailRecipient extends Root
{
    protected $table = "email_recipients";

	public function email()
	{
		return $this->belongsTo('App\Models\Email', 'email_id');
	}

	public function fillInData( $job_id )
	{
		$recipient_bits = explode( '_', $this->recipient );

		if( !empty( $recipient_bits ) )
		{
			switch( $recipient_bits[0] )
			{
				case 'site':
					$this->segment_type = 'site';
					if( !empty( $recipient_bits[1] ) )
					{
						$site = Site::find( $recipient_bits[ 1 ] );

						if( $site )
							$this->name = $site->name;
					}
					break;
				case 'level':
					$this->segment_type = 'level';
					if( !empty( $recipient_bits[1] ) )
					{
						$level = AccessLevel::find( $recipient_bits[1] );

						if( $level )
							$this->name = $level->name;
					}
					break;
				case 'list':
					$this->segment_type = 'list';
					if( !empty( $recipient_bits[1] ) )
					{
						$list = EmailList::find( $recipient_bits[1] );

						if( $list )
							$this->name = $list->name;
					}
					break;
				case 'catch':
					$this->segment_type = 'catch_all';
					$queue_item = EmailRecipientsQueue::withTrashed()->whereEmailJobId( $job_id )->whereEmailRecipientId( $this->id )->first();
					$this->name = 'All members and subscribers';

					if( $queue_item )
					{
						$site = Site::find( $queue_item->site_id );

						$this->name = 'All members of "' . $site->name . '" and subscribers"';

						$user = User::find( $queue_item->sending_user_id );

						if( $user )
						{
							$this->name .= ' that belong to ' . ( !empty( $user->first_name ) ? $user->first_name : $user->email );
						}
					}
					break;
			}
		}

		$link_data = [];

		$links = Link::whereJobId( $job_id )->get();

		foreach( $links as $link )
		{
			$data = [];
			$data['url'] = $link->url;
			$data['id'] = $link->id;
			$data['total_clicks'] = Click::whereSegmentId( $this->id )->whereLinkId( $link->id )->count();
			$data['unique_clicks'] = Click::whereSegmentId( $this->id )->whereLinkId( $link->id )->select('identifier')->groupby('identifier')->distinct()->get()->count();
			$link_data[] = $data;
		}

		$this->links = $link_data;

		$total_clicks = 0;
		$unique_clicks = 0;

		foreach( $this->links as $link )
		{
			$total_clicks += $link['total_clicks'];
			$unique_clicks += $link['unique_clicks'];
		}

		$this->total_clicks = $total_clicks;
		$this->unique_clicks = $unique_clicks;

		$this->total_opens = Open::whereJobId( $job_id )->whereSegmentId( $this->id )->count();
		$this->unique_opens = Open::whereJobId( $job_id )->whereSegmentId( $this->id )->select('identifier')->groupby('identifier')->distinct()->get()->count();

		$this->total_recipients = EmailQueue::withTrashed()->whereJobId( $job_id )->whereEmailRecipientId( $this->id )->count();
	}
}