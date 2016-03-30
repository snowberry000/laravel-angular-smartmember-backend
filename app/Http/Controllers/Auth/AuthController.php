<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\DomainHelper;
use App\Models\User;
use App\Models\VerificationCode;
use App\Models\Site;
use App\Models\Site\Role;
use App\Models\LinkedAccount;
use App\Models\UserMeta;
use Validator;
use App\Models\AppConfiguration\SendGridEmail;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Models\AppConfiguration;
use Input;
use Auth;

class AuthController extends Controller
{
    use AuthenticatesAndRegistersUsers;

    protected $site;

    public function __construct(){
        $this->site = \Domain::getSite();
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    public function anyOptin(){
        $params = ['first_name','last_name','email'];

        $user_data = Input::only($params);        
        $user_options = Input::except($params);

        $user = User::create($user_data);
        $user->email = Input::get('email');
        $user->save();

		UserMeta::saveUserOption( $user_options, $user->id, $this->site );

        return User::with('meta')->whereId($user->id)->first();
    }


    public function getLogout()
    {
        Auth::logout();
        return ['logout' => true];
    }

    public function postSpoof()
    {
        $email = Input::get('email', FALSE);
        //perform SM team check
        $user = Input::get('user');

		if( !empty( $user['id'] ) )
		{
			$user = User::find( $user[ 'id' ] );
		}
		else
		{
			return \App::abort(403,"Sorry you need to be on http://my.smartmember.com to use that feature.");
		}

        $role = Role::whereUserId($user->id)->whereIn('type',['owner','admin'])->whereSiteId(6192)->first();

        if( $role )
        {
            $primaryAccount = LinkedAccount::where('linked_email', $email)
                ->where('verified', 1)
                ->where('email_only_link', 1)->first();

            if ($primaryAccount)
            {
                $u = User::find($primaryAccount->user_id);
                if ($u)
                    $email = $u->email;
            }

            if (!empty($email)) {
                $user = User::whereEmail($email)->first();

                $linkedUser = LinkedAccount::where('linked_email', $user->email)->where('verified', 1)->first();
                if ($linkedUser) {
                    $user = User::find($linkedUser->user_id);
                }

                $data = $user->toArray();
                $data["access_token"] = $user->access_token;

                $data['sm_user'] = Role::whereUserId( $user->id )->whereSiteId(1)->first() ? true : false;

                return $data;

            }

        } else {
            return \App::abort(403,"Sorry you need to be SM Admin to access this feature");
        }

    }

    public function postLogin()
    {
        $email = Input::get('email', FALSE);
        // First check with the linke account if user is logging in with a verified secondary
        // eamil address
        if ( ! empty($email))
        {
            $primaryAccount = LinkedAccount::where('linked_email', $email)
                                            ->where('verified', 1)
											->first();

            if ($primaryAccount)
            {
                $u = User::find($primaryAccount->user_id);
                if ($u)
                    $email = $u->email;
            }

        }

        //I removed the requirement for verification because users were having issues
        if (!empty($email) && Auth::attempt(['email' => $email, 'password' => Input::get("password")])) {
            $user = Auth::user();

            $linkedUser = LinkedAccount::where('linked_email', $user->email)->where('verified', 1)->first();
            if ($linkedUser)
            {
                $user = User::find($linkedUser->user_id);
            }

            $data = $user->toArray();
            $data["access_token"] = $user->access_token;
            
            //Check if its for site
            if ($this->site){
                $data["is_site"] = true;
                $this->site->addMember($user);
            }

            if (Input::has('hash'))
            {
                $hash = Input::get("hash");
                $user->createAccessPass($hash, $this->site);
            }

            if (Input::has('cbreceipt'))
            {
                $cbreceipt = Input::get("cbreceipt");
                $user->createAccessPass('', $this->site, $cbreceipt);
            }

			$user->last_logged_in = date( 'Y-m-d H:i:s');
            //$user->refreshAttributes();//not sure what this is supposed to do, but it causes log in to fail
			$user->save();

            $data['sm_user'] = Role::whereUserId( $user->id )->whereSiteId(1)->first() ? true : false;
            return $data;
        }
        return \App::abort(403,"Email or password is incorrect. Try again");
    }

    public function regenerateAccessToken()
    {
       $user =  User::where('access_token' , \Input::get('access_token'))->first();
       $resp = $user->refreshToken();
       $user->save();
       return $resp;
    }

    public function postUsercheck()
    {
        $user=User::whereEmail(\Input::get("email"))->first();
        if($user==null)
        {
            $user= $this->postRegister();
            //$user = User::create(Input::except("verified", "token", "access_token", "product_hash", "hash", "cbreceipt"));
            return array("status"=>"created","user" => $user);
        }
        else if($user->password=="")
        {
            return array("status"=>"inprocess","user" => $user);
        }
        else
        {
            return array("status"=>"exists");
        }
    }
    public function postRegister()
    {
        $user=User::whereEmail(Input::get("email"))->first();
        if($user)
		{
			$email = Input::get('email', FALSE);

			if ( ! empty($email))
			{
				$primaryAccount = LinkedAccount::where('linked_email', $email)
					->where('verified', 1)
					->first();

				if ($primaryAccount)
				{
					$u = User::find($primaryAccount->user_id);
					if ($u)
						$email = $u->email;
				}

			}

			if( Input::has('verification_code') && !empty( Input::get('verification_code') ) && VerificationCode::VerifyCode( $user->id, Input::get('verification_code') ) )
			{
				if( !empty( Input::get('password') ) )
					$user->password = Input::get("password");

				if( Input::has('first_name') && !empty( Input::get('first_name') ) )
					$user->first_name = Input::get('first_name');

				$user->save();

				\App\Models\Event::Log( 'reset-password-using-verification', array(
					'site_id' => 0,
					'user_id' => $user->id,
					'email' => $user->email,
					'verification-code' => Input::get('verification_code')
				) );
			}
			elseif( Input::has('verification_code') && !empty( Input::get('verification_code') ) && !VerificationCode::VerifyCode( $user->id, Input::get('verification_code') ) )
			{
				\App\Models\Event::Log( 'submitted-invalid-verification-code', array(
					'site_id' => 0,
					'user_id' => $user->id,
					'email' => $user->email,
					'verification-code' => Input::get('verification_code')
				) );
				\App::abort( '403', 'Verification code invalid' );
			}

			if ( !empty($email) && Auth::attempt(['email' => $email, 'password' => Input::get("password")]) ) {
				$user = Auth::user();

				$linkedUser = LinkedAccount::where('linked_email', $user->email)->where('verified', 1)->first();
				if ($linkedUser)
				{
					$user = User::find($linkedUser->user_id);
				}

				$data = $user->toArray();
				$data["access_token"] = $user->access_token;

				//Check if its for site
				if ($this->site){
					$data["is_site"] = true;
					$this->site->addMember($user);
				}

				if (Input::has('hash'))
				{
					$hash = Input::get("hash");
					$user->createAccessPass($hash, $this->site);
				}

				if (Input::has('cbreceipt'))
				{
					$cbreceipt = Input::get("cbreceipt");
					$user->createAccessPass('', $this->site, $cbreceipt);
				}

				$user->last_logged_in = date( 'Y-m-d H:i:s');
				$user->save();

				$data['sm_user'] = Role::whereUserId( $user->id )->whereSiteId(1)->first() ? true : false;
				return $data;
			}

			$verification_code = VerificationCode::create(['user_id' => $user->id ]);

			if( $verification_code )
				SendGridEmail::sendVerificationCodeEmail( $user, $verification_code->code, $this->site );

			\App::abort( '403', 'User email already exists' );
		} else
        //if($user!=null)
        {
            $user=User::create(Input::except("verified", "token", "access_token", "product_hash", "hash", "cbreceipt"));
            $user->password = Input::get('password');
            $user->first_name = Input::get('first_name');
            $user->last_name = Input::get('last_name');
            $user->email = Input::get('email');
            $user->refreshToken();
            $user->refreshEmailHash();
            // $user->save();
            // $user=User::whereEmail(Input::get("email"))->first();

            $user->refreshToken();
            $subdomain = DomainHelper::getSubdomain();
            //if (Input::get('password'))
                //SendGridEmail::sendNewUserEmail($user , $this->site);
            
            $user->verified = 1;
            $user_data = $user->toArray();

			if( !$this->site )
				$this->site = Site::find( 6192 );

            if ($this->site){
                $user_data["is_site"] = true;
                $this->site->addMember($user);
            }

            if (Input::has('hash'))
            {
                $hash = Input::get("hash");
                $user->createAccessPass($hash, $this->site);
            }

            if (Input::has('cbreceipt'))
            {
                $cbreceipt = Input::get("cbreceipt");
                $user->createAccessPass('', $this->site, $cbreceipt);
            }

			$user->last_logged_in = date( 'Y-m-d H:i:s');
            //$user->refreshAttributes();//not sure what this is supposed to do, but it causes log in to fail
            $user->save();
            AppConfiguration::AddMemberToEmailListIntegrationForSite($this->site, $user);
            $user_data['access_token'] = $user->access_token;
            $hash = md5 ( microtime() );

            return $user_data;
        }
        /*else 
        {
            $user=User::create(Input::except("verified", "token", "access_token", "product_hash", "hash", "cbreceipt"));
            $user->email = \Input::get("email");
            $user->save();
            AppConfiguration::AddMemberToEmailListIntegrationForSite($this->site, $user);
            return array("status"=>"initiated email address");
        }*/
    }

    public function postForgot()
    {
        $email = Input::get('email');

        $user = User::whereEmail($email)->first();
        $linked_account = LinkedAccount::whereLinkedEmail($email)->where('verified', 1)->first();
        if(!$user && !$linked_account){
            return array('success'=>false , 'message'=>'no such email found');
        }
        $user->reset_token = md5($email . rand(10000,99999));
        $user->save();
        $custom_token = "";
        if ($linked_account)
        {
            $main_user = User::find($linked_account->user_id);
            if ($main_user)
            {
                $main_user->reset_token =  md5($email . rand(10000,99999));
                $custom_token = $main_user->reset_token;
                $main_user->save();
                SendGridEmail::sendForgotPasswordEmail($main_user, $this->site);
            }
        }

        SendGridEmail::sendForgotPasswordEmail($user, $this->site, $custom_token);

        return array('success'=>true , 'message'=>'Password reset token sent to email');
    }

    public function postReset(){
        if(!Input::get('reset_token'))
            \App::abort(403, "Reset token was not valid");  

        $user = User::withTrashed()->whereResetToken(Input::get('reset_token'))->first();
        if($user){
            $user->password = Input::get('password');
            $user->refreshToken();
            $user->refreshEmailHash();
            $user->save();

            $main_account_linked = LinkedAccount::whereLinkedEmail($user->email)->where('verified',1)->first();
            if ($main_account_linked)
            {
                $main_account = User::find($main_account_linked->user_id);
                $main_account->password = Input::get('password');
                $main_account->refreshToken();
                $main_account->refreshEmailHash();
                $main_account->save();
                \App\Models\Event::Log( 'reset-password', array(
                    'site_id' => 0,
                    'user_id' => $main_account->id,
                    'email' => $main_account->email
                ) );
            }

			\App\Models\Event::Log( 'reset-password', array(
				'site_id' => 0,
				'user_id' => $user->id,
				'email' => $user->email
			) );

            return array('success'=>true);
        }

        \App::abort(403, "Reset token was not valid");      
    }

    /*
        Remove Facebook login
    */
    public function postFacebookLogin()
    {

        $user = User::where(["facebook_user_id" => Input::get("id")])->first();
        if (!$user) {
            $user = new User();
            $user->email = Input::get("email");
            $user->first_name = Input::get("first_name");
            $user->last_name = Input::get("last_name");
            $user->facebook_user_id = Input::get("id");
            $user->verified = 1;
            $user->refreshToken();
            $user->refreshEmailHash();
            $user->save();

            Auth::loginUsingId($user->id);
            $user = Auth::user();
        }

        if ($this->site){
            $data["is_site"] = true;
            $this->site->addMember($user);
        }

        $data = $user->toArray();
        $data["access_token"] = $user->access_token;

        return $data;
    }

    public function getVerify($access_token)
    {
        $user = User::where("access_token", $access_token)->first();
        $user->verified = 1;
        $user->save();
        $data = $user->toArray();
        $data["access_token"] = $user->access_token;
        return redirect(\Domain::appRoute(\Input::get('subdomain'),"/"));
    }
}
