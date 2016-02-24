<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\AppConfiguration\SendGridEmail;
use App\Models\User;
use App\Models\Site;
use App\Models\SiteMetaData;
use App\Models\ImportJob;
use App\Models\Site\Role;
use Auth;

class ImportQueue extends Root
{
    protected $table = "imports_queue";

    public function site()
    {
        return $this->belongsTo('App\Models\Site');
    }

	public static function enqueue($emails, $access_levels, $expiry, $site, $email_welcome = 0, $email_ac = 0)
	{
		$import_job = new ImportJob();
		$import_job->site_id = $site->id;
		$import_job->total_count = count($emails);
		$import_job->save();

		$queue = [];

		foreach ($emails as $email)
		{
			$toQueue = [];
			$toQueue['name'] = $email['name'];
			$toQueue['email'] = $email['email'];
			$toQueue['access_levels'] = trim(implode(',', $access_levels), ",");
			$toQueue['site_id'] = $site->id;
			$toQueue['expiry'] = $expiry;
			$toQueue['job_id'] = $import_job->id;
			$toQueue['email_welcome'] = $email_welcome;
			$toQueue['email_ac'] = $email_ac;

			$queue[] = $toQueue;
		}

		$length           = count( $queue );
		$max_placeholders = 65535;
		if( !empty( $queue[ 0 ] ) )
		{
			$cols = array_keys( $queue[ 0 ] );

			foreach( $cols as $key => $val )
				$cols[ $key ] = '`' . $val . '`';
			$column_count        = count( $cols );
			$max_rows_per_insert = floor( $max_placeholders / $column_count );

			for( $x = 0; $x < $length; $x = $x + $max_rows_per_insert )
			{
				$insert_recipients = array_slice( $queue, $x, $max_rows_per_insert );
				$insert_values     = [ ];

				foreach( $insert_recipients as $recipient )
				{
					foreach( $recipient as $key => $val )
						$recipient[ $key ] = "'" . $val . "'";

					$insert_values[] = '(' . implode( ',', $recipient ) . ')';
				}

				$sql = "INSERT INTO `imports_queue` (" . implode( ',', $cols ) . ') values ' . implode( ',', $insert_values ) . ';';
				\DB::statement( $sql );
			}
		}
	}

    public function lockQueue($site_id)
    {
		$now = Carbon::now();
		SiteMetaData::create(['site_id' => $site_id, 'key' => 'imports_queue_locked', 'value' => $now->timestamp + 1800 ]);
    }

    public function unLockQueue($site_id)
    {
		$imports_queue_locked = SiteMetaData::whereSiteId($site_id)->whereKey('imports_queue_locked')->get();

		foreach( $imports_queue_locked as $lock_item )
			$lock_item->forceDelete();
    }

    public function isQueueLocked($site_id)
    {
		$now = Carbon::now();
		$imports_queue_locked = SiteMetaData::whereSiteId($site_id)->whereKey('imports_queue_locked')->first();
		
		if ($imports_queue_locked && $imports_queue_locked->value > $now->timestamp) {
			return true;
		}

		return false;
    }

	private function nameSplitter($fullName)
	{
		if (strpos($fullName, " ") !== FALSE)
		{
			$parts = explode(" ", $fullName);
			$lastname = array_pop($parts);
			$firstname = implode(" ", $parts);
		} else {
			$firstname = $fullName;
			$lastname = '';
		}
		return array('first_name' => $firstname, 'last_name' => $lastname);
	}

	private function queueHelper($site_id)
	{
		$per_run = 4000;
		$queue_items = ImportQueue::whereSiteId($site_id)->skip(0)->take($per_run)->get();

		$count = 0;
		foreach ($queue_items as $queue_item)
		{
			$user = User::firstOrNew(['email' => $queue_item->email]);
			$newUser = false;
			$password  = '';

			if (! $user->id)
			{
				$user->refreshToken();
				$password = User::randomPassword();
				$user->password = $password;

				if ( !empty( $queue_item->name ) )
				{
					$user->first_name = $queue_item->name;
				}

				$user->email = $queue_item->email;
				$user->verified = 1;
				$user->reset_token = md5( microtime().rand() );
				$user->save();
				$newUser = true;
				$count++;
			} else {
				if ( empty( $user->first_name ) && empty( $user->last_name ) )
				{
					if ( !empty( $queue_item->name ) && empty( $user->first_name ) )
					{
						$user->first_name = $queue_item->name;
						$user->save();
					}
				}
			}
			$granted_passes = [];
			$alreadyExists = Role::whereUserId($user->id)->whereSiteId($queue_item->site_id)->first();
			if (!empty($queue_item->access_levels))
			{
				$access_levels = explode(",", $queue_item->access_levels);
			}

			$site = Site::find( $queue_item->site_id );

			if( !empty( $access_levels ) )
			{
				foreach( $access_levels as $level )
				{
					$access_level = AccessLevel::where( 'id', '=', $level )->first();
					$pass = Role::whereUserId( $user->id )->whereSiteId( $queue_item->site_id )
						->whereAccessLevelId( $level )->whereNull( 'deleted_at' )->first();

					Role::GrantSuperLevel( $level, $user->id );

					if( !$pass )
					{
						$pass              = Role::create( [ 'access_level_id' => $level,
															 'user_id' => $user->id,
															 'site_id' => $queue_item->site_id,
															 'type' => 'member',
															 'expired_at' => $queue_item->expiry
														   ] );

						$pass->site        = $site;
						$pass->user        = $user;
						$pass->accessLevel = $access_level;
						if( !empty( $password ) )
							$pass->password = $password;

						$granted_passes[] = $pass;
					}
				}
			}
			elseif( !$alreadyExists )
			{
				Role::create( [ 'user_id' => $user->id,
					 'site_id' => $queue_item->site_id,
					 'type' => 'member',
				] );
			}

			if(!$alreadyExists)
			{
				$site->total_members = $site->total_members + 1;
				$site->save();
			}

			if( !empty( $granted_passes ) && $queue_item->email_ac)
			{
				SendGridEmail::sendAccessPassEmail($granted_passes);
			}
			else
			{
				if( empty( $password ) )
					$password = '';

				if( !$alreadyExists && $queue_item->email_welcome)
				{
					SendGridEmail::sendNewUserSiteEmail($user, $site, $password );
				}
			}
			$queue_item->delete();
		}

		return array('total_imported_member' => $count);
	}

    public function processQueue($site_id, $abort_on_lock = true)
    {
        $this->lockQueue($site_id);
        $data = $this->queueHelper($site_id);
        $this->unLockQueue($site_id);

        return $data;
    }
}
