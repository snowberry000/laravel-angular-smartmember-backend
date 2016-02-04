<?php namespace App\Models;

use App\Models\Root;
use App\Models\Site\Role as SiteRole;
use App\Models\Site\Role;
use App\Models\TeamRole;
use App\Models\AccessLevel;
use App\Models\AccessLevel\Pass;
use App\Models\AccessLevel\Grant;
use App\Models\AppConfiguration\SendGridEmail;
use App\Models\Site\Capability;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Carbon\Carbon;
use SMCache;
use Config;

class User extends Root implements AuthenticatableContract
{

    use Authenticatable;

    protected $table = 'users';
    protected $guarded = ['email', 'access_token', 'access_token_expired', 'reset_token', 'email_hash'];
    protected $hidden = ['password', 'access_token', 'access_token_expired', 'reset_token'];

    public function pages()
    {
        return $this->hasMany("App\\Models\\Page", "user_id");
    }

    public function sites()
	{
        return $this->belongsToMany("App\Models\Site",'sites_roles','user_id','site_id');
    }

    public function role()
	{
        return $this->hasMany("App\\Models\\Site\\Role");
    }

    public function emailSettings()
    {
        return $this->hasOne("App\\Models\\EmailSetting", 'user_id');
    }

    public function purchases()
    {
        return $this->hasMany("App\Models\Transaction")->whereType('sale');
    }

    public function refunds()
    {
        return $this->hasMany('App\Models\Transaction', 'email', 'email')->whereType('rfnd');
    }

    public function subscriber()
    {
        return $this->hasOne('App\Models\EmailSubscriber', 'email', 'email');
    }

    public function linkedAccounts()
    {
        return $this->hasMany('App\Models\LinkedAccount', 'user_id', 'user_id');
    }

	public static function applySearchQuery($query , $value)
	{
		return $query->where('first_name','like','%' . $value . "%")->orWhere('last_name','like','%' . $value . "%")->orWhere('email','like','%' . $value . "%");
	}

    public function getEmailHashAttribute( $value )
    {
		if( empty( $value ) )
		{
			$email_hash = md5(trim($this->email));
			$this->email_hash = $email_hash;
			$this->save();
		}
		else
		{
			$email_hash = $value;
		}

        return $email_hash;
    }

    public function options($meta_key = null)
    {
        if (!empty($meta_key))
            return $this->hasMany("App\\Models\\UserOptions", "user_id")->where('meta_key', $meta_key);

        return $this->hasMany("App\\Models\\UserOptions", "user_id");
    }

	public function meta($site = null)
	{
		if ( !empty($site) )
		{
			if( is_numeric( $site ) )
				$site_id = $site;
			else
				$site_id = $site->id;

			return $this->hasMany( "App\\Models\\UserMeta", "user_id" )->whereSiteId( $site_id );
		}

		return $this->hasMany("App\\Models\\UserMeta", "user_id");
	}

    public static function randomPassword($length=12)
    {
        $alphabet    = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass        = array(); //remember to declare $pass as an array
        $alphaLength = strlen( $alphabet ) - 1; //put the length -1 in cache

        for( $i = 0; $i < $length; $i++ )
        {
            $n      = rand( 0, $alphaLength );
            $pass[] = $alphabet[ $n ];
        }
        return implode( $pass );
    }

    public function getSiteRole(){
        $this->sites();
    }


    public function setPasswordAttribute($value)
    {
        $this->attributes["password"] = password_hash($value,PASSWORD_BCRYPT,['cost'=>11]);
    }

    public function refreshEmailHash(){
        \Log::info($this->email);
        \Log::info($this->password);
        $this->email_hash = md5($this->email . $this->password);
        return $this->email_hash;
    }

    public function refreshToken()
    {
        $this->access_token_expired = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " + 3 months"));;
        $this->access_token = md5($this->email . $this->access_token_expired . rand(10000, 99999));
    }

    public function createAccessPass($access_hash, $site, $cbreceipt = '')
    {        
        if ($cbreceipt != '')
        {
            $transaction =  Transaction::where('transaction_id', '=', $cbreceipt)->first();
            if (! $transaction)
                return; // TODO Notify user of the problem with product association. They need to contact support at this point.
            if ($transaction->user_id  && $transaction->user_id != $this->id)
            {
                return; // TODO Notify user of the actual error.
            }
            if ($transaction->source == 'jvzoo')
            {
                $access_level = AccessLevel::where('product_id', $transaction->product_id)->first();
            }
            else if ($transaction->source == 'stripe') {
                $access_level = AccessLevel::find($transaction->product_id);
            }

            if ($access_level && $transaction->payment_interval != 'one_time')
            {
                switch ($transaction->payment_interval)
                {
                    case 'weekly':
                        $expiration = Carbon::now()->addWeeks(1);
                        break;
                    case 'monthly':
                        $expiration = Carbon::now()->addMonths(1);
                        break;
                    case 'annual':
                        $expiration = Carbon::now()->addYears(1);
                        break;
                    default:
                        $expiration = "";
                        break;
                }

                $pass = Role::create(['access_level_id' => $access_level->id,
				    'type' => 'member',
                    'user_id' => $this->id,
                    'site_id' => $access_level->site_id,
                    'expired_at' => $expiration]);
            } else {

                $pass = Role::create(['access_level_id' => $access_level->id,
				    'type' => 'member',
                    'user_id' => $this->id,
                    'site_id' => $access_level->site_id
                ]);
            }
        } else {
            $access_level = AccessLevel::where(['hash' => $access_hash])->first();

            if ( !$access_level )
            {
                $transaction =  Transaction::where('association_hash', $access_hash)->first();
                if (! $transaction)
                    return; // TODO Notify user of the problem with product association. They need to contact support at this point.

                if ($transaction->user_id  && $transaction->user_id != $this->id)
                {
                    return; // TODO Notify user of the actual error.
                }
                if ($transaction->source == 'jvzoo')
                {
                    $access_level = AccessLevel::where('product_id', $transaction->product_id)->first();
                }
                else if ($transaction->source == 'stripe') {
                    $access_level = AccessLevel::find($transaction->product_id);
                }

                if ($access_level && $transaction->payment_interval != 'one_time')
                {
                    switch ($transaction->payment_interval)
                    {
                        case 'weekly':
                            $expiration = Carbon::now()->addWeeks(1);
                            break;
                        case 'monthly':
                            $expiration = Carbon::now()->addMonths(1);
                            break;
                        case 'annual':
                            $expiration = Carbon::now()->addYears(1);
                            break;
                        default:
                            $expiration = "";
                            break;
                    }
                    $pass = Role::create(['access_level_id' => $access_level->id,
					    'type' => 'member',
                        'user_id' => $this->id,
                        'site_id' => $access_level->site_id,
                        'expired_at' => $expiration]);
                }
                else {

                    $pass = Role::create(['access_level_id' => $access_level->id,
					    'type' => 'member',
                        'user_id' => $this->id,
                        'site_id' => $access_level->site_id
                    ]);
                }

                if ( isset($transaction) )
                {
                    if(isset($access_level->site_id) && $access_level->site_id == 6192){
                        $three_c = Site::whereSubdomain('3c')->first();
                        $help = Site::whereSubdomain('help')->first();

                        if($three_c && isset($three_c->id)){
                            $data['site_id'] = $three_c->id;
                            SiteRole::create($data);
                        }

                        if($help && isset($help->id)){
                            $data['site_id'] = $help->id;
                            SiteRole::create($data);
                        }
                    }

                    $transaction->user_id = $this->id;
                    $transaction->save();
                }

            }
			else
			{
                $data = array("access_level_id" => $access_level->id, 'type' => 'member', "user_id" => $this->id, 'site_id' =>$access_level->site_id);
                $pass = SiteRole::create($data);
            }
        }

		if( !empty( $pass ) )
			return $pass;
		else
			return array( 'success' => false );
    }


    public function hasSMPass()
    {
        $required_level = \Config::get('vars.member_access_level', 1753);

        $role = SiteRole::where('access_level_id', $required_level)
            ->where('user_id', $this->id)
            ->first();

        if (!isset($role->id)) {
            $passes = Role::whereUserId(\Auth::user()->id)->get();
            foreach ($passes as $pass) {
                $access_levels = Pass::access_levels($pass->access_level_id);

                if ($pass && in_array($required_level, $access_levels)) {
                    return true;
                }
            }

            return false;
        } else {
            return true;
        }
    }

    public static function gravatarImage( $email, $s = 80, $d = 'mm', $r = 'g' )
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";

        return $url;
    }

    public function associateProduct($product_hash, $site, $cbreceipt = '')
    {

        if ( !$product_hash || !$site) return;

        $access_level = AccessLevel::where(['hash' => $product_hash])->first();

        $expiration = 0;

        if ( !$access_level )
        {
            $transaction =  Transaction::where('association_hash', $product_hash)
                                        ->orWhere('transaction_id', $cbreceipt)->first();

            if (! $transaction)
                return; // TODO Notify user of the problem with product association. They need to contact support at this point.

            if ($transaction->user_id  && $transaction->user_id != $this->id)
            {
                return; // TODO Notify user of the actual error.
            }

            if ($transaction->source == 'jvzoo')
            {
                $access_level = AccessLevel::where('product_id', $transaction->product_id)->first();
            }
            else if ($transaction->source == 'stripe')
            {
                $access_level = AccessLevel::find($transaction->product_id);

                if ($access_level && $transaction->payment_interval != 'one_time')

                {
                    switch ($transaction->payment_interval)
                    {
                        case 'weekly':
                            $expiration = Carbon::now()->addWeeks(1);
                            break;
                        case 'monthly':
                            $expiration = Carbon::now()->addMonths(1);
                            break;
                        case 'annual':
                            $expiration = Carbon::now()->addYears(1);
                            break;
                    }
                }
            }
        } else {
            $data = array("access_level_id" => $access_level->id, "type" => 'member', "user_id" => $this->id, 'site_id' =>$access_level->site_id);
            $role = Role::create($data);
        }

        if ( !$access_level)
        {
            \Log::error("An access level was not found against the params( product_hash = $product_hash, cbreceipt = $cbreceipt");
            return;
        }

        \Log::info($access_level);

        if ( ! $site || $access_level->site_id != $site->id ) 
        {
            \Log::error("The site $site->id doesn't own the access level $access_level->id ");
            return;
        }

        if( !isset($transaction) || ( isset($transaction) && !$transaction->user_id) )
        {
            $role = Role::create(['access_level_id' => $access_level->id,
							     'type' => 'member',
                                 'user_id' => $this->id,
                                 'site_id' => $site->id,
                                 'expired_at' => $expiration]);

            if ( isset($transaction) )
            {
                $transaction->user_id = $this->id;
                $transaction->save();
            }
        }

    }

    //TODO: import is sending emails right away, It will be panifully slow for bulk
    // import of large number of users. Instead enqueue emails. 
    public static function importUsers($emails, $access_levels, $expiry, $site)
    {
        if ( ! $site ) return;

        $count = 0;
        foreach ($emails as $email) 
        {
            $user = User::firstOrNew(['email' => $email]);
            $newUser = true;
            $password  = '';

            if (! $user->id)
            {
                $user->refreshToken();
                $password = self::randomPassword();
                $user->password = $password;
                $user->email = $email;
                $user->verified = 1;
                $user->reset_token = md5( microtime().rand() );
                $user->save();
                $newUser = true;
                $count++;
            }

			$granted_passes = [];
            $alreadyExists = Role::whereUserId($user->id)->whereSiteId($site->id)->whereNull('deleted_at')->first();
            foreach ($access_levels as $level)
            {
                $pass = Role::whereUserId($user->id)->whereSiteId($site->id)->whereAccessLevelId($level)->whereNull('deleted_at')->first();
                if (!$pass)
                {
                    $pass = Role::create(['access_level_id' => $level,
                        'user_id' => $user->id,
                        'site_id' => $site->id,
                        'type' => 'member',
                        'expired_at' => $expiry
                    ]);
                    
                    $pass->site = $site;
                    $pass->user = $user;
                    $pass->accessLevel = AccessLevel::where('id','=',$level)->first();
					if( !empty( $password ) )
						$pass->password = $password;

					$granted_passes[] = $pass;
                }
            }

            if(!$alreadyExists)
            {
                $site->total_members = $site->total_members + 1;
                SendGridEmail::sendNewUserSiteEmail($user, $site, $password,false);
                $site->save();
            }

			if( !empty( $granted_passes ) )
			{
				SendGridEmail::sendAccessPassEmail($granted_passes);
			}

        }

        return $count;
    }

    public function linkAccount($email = FALSE , $verified=0 , $claimed = 0)
    {
        $response = ['status' => 'NOTOK', 'message' => ''];
        if ( !$email ) return $response;

        $already_linked = LinkedAccount::where('linked_email', $email)->first();
        if ($already_linked)
        {
            $response['message'] = 'The given email is already linked';
            if ($already_linked->user_id != $this->id)
                $response['message'] .= ' with another account';

            return $response;
        }

        $account = User::whereEmail($email)->first();
        $linked_account = new linkedAccount;
        
        $linked_account->user_id = $this->id;
        $linked_account->linked_email = $email;

        if ( !$account )
        {
            $linked_account->email_only_link = 1;
        }
        else
        {
            $linked_account->linked_user_id = $account->id;
        }

        $linked_account->verification_hash = md5($this->email . rand(10000, 99999));
        $linked_account->verified = $verified;
        $linked_account->claimed = $claimed;
        $linked_account->save();

        $response['status'] = 'OK';
        $response['account'] = $linked_account;

        SendGridEmail::sendAccountLinkEmail($linked_account);
        
        return $response;
    }

    public function resendVerification($email = FALSE)
    {
        if (!$email) return;

        $acct = LinkedAccount::where('user_id', $this->id)
                      ->where('linked_email', $email)
                      ->where('verified', 0)
                      ->first();

        if (!$acct) return;

        SendGridEmail::sendAccountLinkEmail($acct);

        return ['status' => 'OK'];
    }

	/**
	 * Grabs all the sites a user has a specific capability on
	 * @param string $capability The capability to grab the list of site's the user has it on
	 * @param bool|true $return_sites defaults to true to return the actual site objects, false returns just site ids
	 * @return mixed Collection of sites if $return_sites is true, otherwise an array of sites
	 */
	public function sitesWithCapability( $capability, $return_sites = true )
	{
		$roles        = $site_ids = [ ];
		$role_sites = [ 'all' => [] ];
		$system_roles = Config::get( 'roles.roles' );

		foreach( $system_roles as $key => $val )
			if( in_array( $capability, $val ) )
			{
				$roles[]      = $key;
				$role_sites['all'][] = $key;
			}

		$custom_roles = Capability::whereCapability( $capability )->get();

		foreach( $custom_roles as $custom_role )
		{
			$roles[]       = $custom_role->type;

			if( empty( $role_sites[ $custom_role->site_id ] ) )
				$role_sites[ $custom_role->site_id ] = [];

			$role_sites[ $custom_role->site_id ][] = $custom_role->type;
		}

		$roles = Role::whereIn( 'type', $roles )->whereUserId( $this->id )->get();

		foreach( $roles as $role )
		{
			if( in_array( $role->type, $role_sites['all'] ) && !in_array( $role->site_id, $site_ids ) )
				$site_ids[] = $role->site_id;
			elseif( !empty( $role_sites[ $role->site_id ] ) && in_array( $role->type, $role_sites[ $role->site_id ] )  && !in_array( $role->site_id, $site_ids ) )
				$site_ids[] = $role->site_id;
		}

		if( !empty( $site_ids ) )
		{
			if( !$return_sites )
				return $site_ids;

			$sites = Site::whereIn( 'id', $site_ids )->get();
			return $sites;
		}

		return [];
	}
}

User::creating(function ($user) {
    //Don't allow duplicate emails
    if (User::whereEmail($user->email)->first()) {
        \App::abort('401', 'Email address already taken');
    }
    return true;
});

User::saving(function($user){

    $routes[] = 'user_';
    
    SMCache::reset($routes);
    return $user;
});

User::deleting(function($user){

    $routes[] = 'user_';
    
    SMCache::reset($routes);
    return $user;
});
