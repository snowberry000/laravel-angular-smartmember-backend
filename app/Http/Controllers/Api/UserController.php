<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\EmailSubscriber;
use App\Models\User;
use App\Models\UserOptions;
use App\Models\Site\Role;
use App\Models\Site;
use App\Models\AccessLevel;
use App\Models\AppConfiguration\SendGridEmail;
use App\Models\VerificationCode;
use App\Models\AccessLevel\Pass;
use App\Models\TeamRole;
use App\Models\UserRole;
use App\Models\Transaction;
use App\Models\AppConfiguration;
use App\Models\LinkedAccount;
use Auth;

class UserController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new User();

		$this->middleware('auth',['except' => array('transactionAccount','saveTransactionAccount','registerTransactionAccount','associateTransactionAccount','sendVerificationCode') ] );
    }

    public function index(){
    	\App::abort('401',"You don't have access to this resource");
    }

    public function store()
    {
        \App::abort('401',"You don't have access to this resource");
    }

    public function show($model){
        if (Auth::check())
        {
            $user = Auth::user();

            if ( $user->id == $model->id)
            {
				$model = $model->with('role')->find($model->id);
                $model->user_options = UserOptions::whereUserId($model->id)->get();
                $model->linked_accounts = LinkedAccount::whereUserId($model->id)->get();
                $model->sm_access = $user->hasSMPass();
				$model->setup_wizard_complete = intval( $model->setup_wizard_complete );

                return $model;
            }
        }    	
    	\App::abort('401',"You don't have access to this resource");
    }

    public function saveFacebookGroupOption()
    {
        if (\Input::has('user_options'))
        {
            $groups_joined = UserOptions::whereUserId(\Input::get('user_id'))->whereMetaKey('fb_group_joined')->first();

            if( $groups_joined )
                $existing_groups = explode( ',', $groups_joined->meta_value );
            else
                $existing_groups = array();

            $user_options = \Input::get('user_options');

            if( !in_array( $user_options[ 'fb_group_joined' ], $existing_groups ) )
                $existing_groups[] = $user_options[ 'fb_group_joined' ];

            $user_options[ 'fb_group_joined' ] = implode( ',', $existing_groups );

            UserOptions::saveUserOption($user_options, \Input::get('user_id'));
        }
        $app_configuration_instances = AppConfiguration::whereSiteId($this->site->id)->whereType('facebook_group')->get();
        $groups_joined = UserOptions::whereUserId(\Input::get('user_id'))->whereMetaKey('fb_group_joined')->first();
        if (isset($groups_joined))
        {
            foreach ($app_configuration_instances as $key => $value)
            {
                if ($value->remote_id != $groups_joined->meta_value)
                    unset($app_configuration_instances[$key]);
            }
            return $app_configuration_instances;

        } else {
            return array();
        }
    }

    public function saveUserOptions()
    {
        if (\Input::has('user_options'))
        {
            UserOptions::saveUserOption(\Input::get('user_options'), \Input::get('user_id'));
        }
        $user = User::find(\Input::get('user_id'));
        return $this->show($user);
    }

    public function update($model){
    	if (Auth::user()->id == $model->id){
            if (\Input::has('link')) 
            {
                // linking account
                return $model->linkAccount(\Input::get('email', FALSE));
            }

            // $model->email_hash = $model->refreshEmailHash();
    		return parent::update($model);
    	}
    	\App::abort('401',"You don't have access to this resource");
    }

    public function delete($model){
    	if (Auth::user()->id == $model->id){
    		return parent::delete($model);
    	}
    	\App::abort('401',"You don't have access to this resource");
    }

    public function isSuperAdmin() {
        if( $this->site->subdomain != 'sm' || !\SMRole::hasAccess( $this->site->id ,'view_restricted_content' ) )
            return array( 'isSuperAdmin'=>false );
		else
			return array( 'isSuperAdmin'=>true);
    }

    public function changePassword(){
        if (Auth::attempt(['email' => \Auth::user()->email, 'password' => \Input::get("current_password")]))
        {
            $newpassword = \Input::get('newpassword');
            $user = \Auth::user();
            $user->password = $newpassword;
            $user->refreshEmailHash();
            $user->save();
            return array('success'=>true , 'message'=>'Password changed successfully');
        }
        return \App::abort(403,"Wrong current password");
    }

    public function linkAccount()
    {
        if (Auth::user())
        {
            $user = Auth::user();
            return $user->verifyAndLinkAccount(\Input::get('verification_hash', FALSE));
        }

    }

    public function resendVerificationCode()
    {
        if (Auth::user())   
        {
            $user = Auth::user();
            return $user->resendVerification(\Input::get('email', FALSE));
        }
    }

	public function transactionAccount( $transaction )
	{
		$transaction = Transaction::whereTransactionId( $transaction )->whereType('sale')->first();

		if( $transaction && !empty( $transaction->user_id ) )
		{
			$user = $this->model->whereId( $transaction->user_id )->first();

			if( $user )
				return $user;
		}
		else
			return [];
	}

	public function transactionAccess( $transaction )
	{
		$transaction = Transaction::whereTransactionId( $transaction )->whereType('sale')->first();
		if ($transaction)
		{
			if( $transaction->source == 'jvzoo' )
				$access_level = AccessLevel::whereProductId( $transaction->product_id )->first();
			else
				$access_level = AccessLevel::whereId( $transaction->product_id )->first();

			if( $access_level )
			{
				$user = \Auth::user();

				$access_pass = Role::whereAccessLevelId( $access_level->id )->whereUserId( $user->id )->where(function($q){
					$now = date( 'Y-m-d H:i:s');

					$q->whereNull('expired_at');
					$q->orwhere('expired_at','0000-00-00 00:00:00');
					$q->orwhere('expired_at','>', $now );
				})->first();

				if( $access_pass )
					return ['success'=>true];
				else
				{
					$site = Site::find( $access_level->site_id );
					$user->createAccessPass( $access_level->hash, $site );

					return ['success'=>true];
				}
			}
		}

		\App::abort(403,"Transaction not found. Please check back again");
	}

	public function sendVerificationCode()
	{
		$user = $this->model->whereEmail(\Input::get("email"))->first();

		$verification_code = VerificationCode::create(['user_id' => $user->id ]);

		if( $verification_code )
		{
			SendGridEmail::sendVerificationCodeEmail( $user, $verification_code->code, $this->site );

			return ['success'=>true];
		}

		return ['success'=>false];
	}

	public function mergeAccounts($primary_user,$linked_user)
	{
		$linked = $primary_user->linkAccount($linked_user->email);

		$linked_account = new LinkedAccount();

		return $linked_account->merge($linked['account']->verification_hash, $primary_user->id );
	}

	public function saveTransactionAccount()
	{
		if( \Input::has( 'id' ) )
			$user = $this->model->whereId( \Input::get( 'id' ) )->whereNull('last_logged_in')->first();

		if( !empty( $user ) )
		{
			$duplicate_user = $this->model->whereEmail(\Input::get("email"))->first();

			if($duplicate_user && $duplicate_user->id != $user->id )
			{
				if( Auth::attempt(['email' => \Input::get('email'), 'password' => \Input::get('password')]) )
				{
					$this->mergeAccounts( $duplicate_user, $user );

					$user = $duplicate_user;
				}
				elseif( \Input::has('verification_code') && !empty( \Input::get('verification_code') ) )
				{
					if( VerificationCode::VerifyCode( $duplicate_user->id, \Input::get('verification_code') ) )
					{
						$duplicate_user->password = \Input::get('password');
						$duplicate_user->save();

						$this->mergeAccounts( $duplicate_user, $user );

						$user = $duplicate_user;
					}
					else
					{
						\App::abort( '403', 'Verification code invalid' );
					}
				}
				else
				{
					$this->sendVerificationCode();
					\App::abort( '403', 'User email already exists' );
				}
			}
			else
			{
				$user->email          = \Input::get( 'email' );
				$user->password       = \Input::get( 'password' );
				$user->last_logged_in = date( 'Y-m-d H:i:s' );
				$user->refreshEmailHash();
				$user->save();
			}

			$user_data = $user->toArray();

			$user_data[ 'access_token' ] = $user->access_token;

			return $user_data;
		}

		\App::abort(403,"Something went wrong, please try again.");
	}

	public function associateTransactionAccount()
	{
		$transaction = Transaction::whereTransactionId( \Input::get('transaction') )->whereType('sale')->first();

		if (Auth::attempt(['email' => \Input::get('email'), 'password' => \Input::get('password')]))
		{
			$user = \Auth::user();
			$user->last_logged_in = date( 'Y-m-d H:i:s');
			$user->refreshEmailHash();
			$user->save();

			if( $this->site )
				$this->site->addMember($user,'member', '', true );

			if( $transaction->source == 'jvzoo' )
				$access_level = AccessLevel::whereProductId( $transaction->product_id )->first();
			else
				$access_level = AccessLevel::whereId( $transaction->product_id )->first();

			if( $access_level )
			{
				$user = \Auth::user();

				$has_access = false;

				$access_pass = Role::whereSiteId( $access_level->id )->whereAccessLevelId( $access_level->id )->whereUserId( $user->id )->where(function($q){
					$now = date( 'Y-m-d H:i:s');

					$q->whereNull('expired_at');
					$q->orwhere('expired_at','0000-00-00 00:00:00');
					$q->orwhere('expired_at','>', $now );
				})->first();

				if( !$access_pass )
				{
					Role::create([
						 'user_id' => $user->id,
						 'access_level_id' => $access_level->id,
						 'site_id' => $access_level->site_id
					 ]);

					\App\Models\Event::Log( 'connected-to-a-jvzoo-receipt', array(
						'site_id' => $access_level->site_id,
						'user_id' => $user->id,
						'email' => $user->email,
						'access-level-id' => $access_level->id
					) );
				}
			}

			$user_data = $user->toArray();

			$user_data['access_token'] = $user->access_token;

			return $user_data;
		}

		\App::abort(403,"Either your password or e-mail were incorrect.");
	}

	public function associateHash()
	{
		if( \Input::has('hash') && \Auth::user() && $this->site )
		{
			$hash = \Input::get("hash");

			\Auth::user()->createAccessPass( $hash, $this->site );
		}
	}

	public function registerTransactionAccount()
	{
		$transaction = Transaction::whereTransactionId( \Input::get('transaction') )->whereType('sale')->first();

		if( !empty( $transaction ) )
		{
			$user = $this->model->whereEmail(\Input::get("email"))->first();

			if($user)
			{
				if( Auth::attempt(['email' => \Input::get('email'), 'password' => \Input::get('password')]) )
				{

				}
				elseif( \Input::has('verification_code') && !empty( \Input::get('verification_code') ) )
				{
					if( VerificationCode::VerifyCode( $user->id, \Input::get('verification_code') ) )
					{
						$user->password = \Input::get('password');
						$user->save();
					}
					else
					{
						\App::abort( '403', 'Verification code invalid' );
					}
				}
				else
				{
					$this->sendVerificationCode();
					\App::abort( '403', 'User email already exists' );
				}
			}
			else
			{
				$user                 = $this->model->create( [ 'first_name' => \Input::get( 'first_name' ), 'email' => \Input::get( 'email' ) ] );
				$user->email          = \Input::get( 'email' );
				$user->password       = \Input::get( 'password' );
				$user->last_logged_in = date( 'Y-m-d H:i:s' );
				$user->refreshEmailHash();
				$user->refreshToken();
				$user->verified = 1;
				$user->save();
			}

			$this->site->addMember($user,'member', '', true );

			if( $transaction->source == 'jvzoo' )
				$access_level = AccessLevel::whereProductId( $transaction->product_id )->first();
			else
				$access_level = AccessLevel::whereId( $transaction->product_id )->first();

			if( $access_level )
			{
				Role::create([
					 'user_id' => $user->id,
					 'access_level_id' => $access_level->id,
					 'site_id' => $access_level->site_id
				 ]);
			}

			\App\Models\Event::Log( 'connected-to-a-jvzoo-receipt', array(
				'site_id' => $access_level->site_id,
				'user_id' => $user->id,
				'email' => $user->email,
				'access-level-id' => $access_level->id
			) );

			$user_data = $user->toArray();

			$user_data['access_token'] = $user->access_token;

			return $user_data;
		}

		\App::abort(403,"Something went wrong, please try again.");
	}

	public function getSites()
	{
		if( \Input::has('capability') && !empty( \Input::get('capability') ) )
			return \Auth::user()->sitesWithCapability( \Input::get('capability') );
		else
			return \Auth::user()->sites;
	}

	public function getMembers()
	{
		if (\Input::has('type') && !empty(\Input::get('type')))
		{
			$type = \Input::get('type');

			switch ($type)
			{
				case 'member':
					$site_ids = \Auth::user()->sitesWithCapability('manage_members', false);
					$query = Role::with('user')->whereIn('site_id', $site_ids);
					break;
				case 'subscriber':
					$site_ids = \Auth::user()->sitesWithCapability('manage_email', false);
					$query = EmailSubscriber::whereIn('site_id', $site_ids);
					break;
			}

			$page_size = config("vars.default_page_size");
			$query = $query->orderBy('id' , 'DESC');
			$query = $query->whereNull('deleted_at');
			foreach (\Input::all() as $key => $value){
				switch($key){
					case 'view':
					case 'p':
					case 'bypass_paging':
					case 'type':
						break;
					default:
						$query->where($key,'=',$value);
				}
			}

			$return = [];
			if ($type == 'member')
			{
				$count = \DB::table('sites_roles')->select(\DB::raw(' COUNT( DISTINCT user_id ) AS num'))->whereIn('site_id', $site_ids)->first();
				$return['total_count'] = $count->num;
				$query = $query->distinct()->groupBy('user_id');
			} else {
				$return['total_count'] = $query->count();
			}

			if( !\Input::has('bypass_paging') || !\Input::get('bypass_paging') )
				$query = $query->take($page_size);

			if( \Input::has('p') )
				$query->skip((\Input::get('p')-1)*$page_size);

			$return['items'] = $query->get();
			return $return;
		}
	}
}