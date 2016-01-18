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

	public static function enqueue($emails, $access_levels, $expiry, $site)
	{
		$import_job = new ImportJob();
		$import_job->site_id = $site->id;
		$import_job->total_count = count($emails);
		$import_job->save();

		$queue = [];

		foreach ($emails as $email)
		{
			$toQueue = [];
			$toQueue['email'] = $email;
			$toQueue['access_levels'] = trim(implode(',', $access_levels), ",");
			$toQueue['site_id'] = $site->id;
			$toQueue['expiry'] = $expiry;
			$toQueue['job_id'] = $import_job->id;

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
		SiteMetaData::create(['site_id' => $site_id, 'key' => 'import_queue_locked', 'value' => $now->timestamp + 300 ]);
    }

    public function unLockQueue($site_id)
    {
		$email_queue_locked = SiteMetaData::whereSiteId($site_id)->whereKey('import_queue_locked')->first();

		if( $email_queue_locked )
			$email_queue_locked->delete();
    }

    public function isQueueLocked($site_id)
    {
		$now = Carbon::now();
		$email_queue_locked = SiteMetaData::whereSiteId($site_id)->whereKey('email_queue_locked')->first();

		if (isset($email_queue_locked)) {
			return true;
		}

		return false;
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
				$user->email = $queue_item->email;
				$user->verified = 1;
				$user->reset_token = md5( microtime().rand() );
				$user->save();
				$newUser = true;
				$count++;
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

					if(isset($access_level->site_id) && $access_level->site_id == 6192)
					{
						$subdomains = ['dpp1' , 'dpp2' , 'dpp3' , '3c' , 'help' , 'jv' , 'sm'];
						$chosen_access_level = 'Smart Member 2.0';
						$new_data = ['user_id' => $user->id, 'type' => 'member' ];
						foreach ($subdomains as $key => $subdomain) {
							$new_site = Site::whereSubdomain($subdomain)->first();
							if($new_site && isset($new_site->id)){
								$new_data['site_id'] = $new_site->id;
								$new_access_level = AccessLevel::whereSiteId($new_site->id)->where('name' , '=' , $chosen_access_level)->first();
								if($new_access_level && isset($new_access_level->id)){
									$new_data['access_level_id'] = $new_access_level->id;
								}
								Role::create($new_data);
							}
						}
					}

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

			if( !empty( $granted_passes ) )
			{
				SendGridEmail::sendAccessPassEmail($granted_passes);
			}
			else
			{
				if( empty( $password ) )
					$password = '';

				if( !$alreadyExists )
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
