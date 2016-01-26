<?php namespace App\Models\AppConfiguration;

use App\Models\AppConfiguration;
use App\Models\Site;
use App\Models\Site\Role;
use App\Models\TeamRole;
use App\Models\EmailSetting;
use App\Models\SupportTicketAction;
use App\Models\User;

class SendGridEmail {
    
    public $is_debugging = false;

    public function __construct()
    {
    	$this->type = "sendgrid";
        $this->auth_type = "basic";
    }

    //TODO: Refactor email methods, separate logic to have a single method for
    // picking templates. This will reduce clutter in here. 
    public static function sendNewUserEmail($user , $site) 
    {
        $email = new \SendGrid\Email();
        if (isset($site))
        {
            $site_logo = $site->meta_data()->where('key', 'site_logo')->select(['value'])->first();
            $header_bg_color = $site->getHeaderBackgroundColor();
        }
        $view = \View::make("email.user.new", [
                'verify_url' =>  \Domain::apiPath('/auth/verify') . '/'. $user->access_token . '?subdomain='.$site->subdomain,
                'subdomain' => $site->subdomain,
                'site_logo' => isset($site_logo) ? $site_logo->value : '',
                'header_bg_color' => !empty( $header_bg_color ) ? $header_bg_color : ''
            ])->render();

		$from = "noreply@" . ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' );

        $email->addTo($user['email'])
            ->setFrom($from)
            ->setSubject("Welcome to " . ( isset($site->subdomain) ? $site->subdomain : 'Smartmember')  )
            ->setHtml($view);

        self::sendEmail($email, true, $site);
    }

    public static function sendNewSiteEmail($user , $site) 
    {
        $email = new \SendGrid\Email();

        $site_logo = $site->meta_data()->where('key', 'site_logo')->select(['value'])->first();
        $header_bg_color = $site->getHeaderBackgroundColor();

        $view = \View::make("email.user.newsite", [
                'subdomain' => $site->subdomain,
                'site_name' => $site->name,
                'site_logo' => isset($site_logo) ? $site_logo->value : '',
                'header_bg_color' => !empty( $header_bg_color ) ? $header_bg_color : ''
            ])->render();

		$from = "noreply@" . ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' );

        $email->addTo($user['email'])
            ->setFrom($from)
            ->setSubject("New Site Created at " . ( isset($site->subdomain) ? $site->subdomain . '.' : '') . 'smartmember.com'  )
            ->setHtml($view);

        self::sendEmail($email, true, $site);
    }

	public static function getLoginInfo( $user, $site, $password='', $access_level_name = '' )
	{
		$reset_url = "http://";
		if ($site->domain)
			$reset_url .= $site->domain;
		else
			$reset_url .= $site->subdomain . ".smartmember.com";

		$login_url = $reset_url . '?signin';

		$reset_url .="?forgot";
		$string = '<ul style="font-size:17px;line-height:24px;margin:0 0 16px;margin-bottom:1.5rem;list-style:none;padding-left:1rem">';

		if( !empty( $access_level_name ) )
			$string .= "<li>You have been granted access to: <strong>" . $access_level_name . "</strong></li>";

		$string .= "<li><strong>E-mail:</strong> $user->email</li><li><strong>Password:</strong>";

		$string .= !empty( $password ) ? $password : "use your existing password";

		$string .= "</li><li>forgot your password? Click here:<br><a href=\"$reset_url\">$reset_url</a></li>";

		$string .= '</ul>';

		$string .= '<hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">
						<div style="text-align:center;margin:2rem 0">
                            <table cellpadding="0" cellspacing="0"
                                style="border-collapse:collapse;background:#2ab27b;border-bottom:2px solid #1f8b5f;border-radius:4px;padding:14px 32px;display:inline-block">
                                <tbody>
                                    <tr>
                                        <td style="border-collapse:collapse">
                                                <a href="' . $login_url . '"
                                                style="color:white;font-weight:normal;text-decoration:none;word-break:break-word;display:inline-block;letter-spacing:1px;font-size:20px;line-height:26px"
                                                align="center" target="_blank">
                                                    Click here to sign in
                                                </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>';

		return $string;
	}
    
	public static function getWelcomeDefaultSubject()
	{
		$default_subject = 'Welcome to %site_name%';

		return $default_subject;
	}

	public static function getWelcomeDefaultContent()
	{
		$default_email_content = '<h2 style="color:#2ab27b;line-height:30px;margin-bottom:12px;margin:0 0 12px">You\'re in!</h2>
								 	<p style="font-size:18px;line-height:24px;margin:0 0 16px;">
								 		You\'re now a member at <strong>%site_name%</strong> - welcome!
									</p>
								 	<p style="font-size:20px;line-height:26px;margin:0 0 16px">
								 		<strong>Ready to login?</strong> Below you\'ll find your login details and a link to get started.
								 	</p>
								 	<hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">
								 	%login_details%';

		return $default_email_content;
	}

    public static function getLoginButton($site)
    {
        $login_button = '<div style="text-align:center;margin:2rem 0">
                            <table cellpadding="0" cellspacing="0"
                                style="border-collapse:collapse;background:#2ab27b;border-bottom:2px solid #1f8b5f;border-radius:4px;padding:14px 32px;display:inline-block">
                                <tbody>
                                    <tr>
                                        <td style="border-collapse:collapse">
                                                <a href="' . '"
                                                style="color:white;font-weight:normal;text-decoration:none;word-break:break-word;display:inline-block;letter-spacing:1px;font-size:20px;line-height:26px"
                                                align="center" target="_blank">
                                                    Click here to sign in to %site_subdomain%
                                                </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>';
        // $login_button = '<button style="background-color : green">' .
        //                 '<a href="%site_url%"' .
        //                     'style="color:white;font-weight:normal;text-decoration:none;word-break:break-word;display:inline-block;letter-spacing:1px;font-size:20px;line-height:26px" align="center" target="_blank">'.
        //                     'Click here to sign in to %site_subdomain%</a></button>';

        return $login_button;
    }

    public static function sendNewUserSiteEmail($user, $site, $password = '', $cbreceipt = false)
    {
        $email = new \SendGrid\Email();
		$default_subject = self::getWelcomeDefaultSubject();
		$default_email_content = self::getWelcomeDefaultContent();

		$site_welcome_subject = $site->meta_data()->where('key', 'welcome_email_subject')->select(['value'])->first();
		$site_welcome_subject = isset( $site_welcome_subject ) ? $site_welcome_subject->value : $default_subject;

		$site_welcome_subject = str_replace( '%site_name%', $site->name, $site_welcome_subject );

		$site_welcome_content = $site->meta_data()->where('key', 'welcome_email_content')->select(['value'])->first();
		$site_welcome_content = isset( $site_welcome_content ) ? $site_welcome_content->value : $default_email_content;

		$replacements = [
			'%site_name%' => $site->name,
			'%login_details%' => self::getLoginInfo( $user, $site, $password ),
            '%site_url%' => $site->domain ? $site->domain . '/sign/in/' : 'http://'.$site->subdomain . '.smartmember.com/sign/in/',
		    '%site_subdomain%' => $site->subdomain,
			'%login_button%' => '' //this is to get rid of it for anyone who already implemented it
        ];
        \Log::info($site->domain);
		$site_welcome_content = str_replace( array_keys( $replacements ), array_values( $replacements ), $site_welcome_content );

        $site_logo = $site->meta_data()->where('key', 'site_logo')->select(['value'])->first();
        $header_bg_color = $site->getHeaderBackgroundColor();

		$subdomain = $site->subdomain == 'sm' ? 'my' : $site->subdomain;
        if (!empty($site->domain))
        {
            $reset_link = 'http://' . strtolower($site->domain) . '/sign/forgot';
            $login_url = 'http://' . strtolower($site->domain) . '/sign/in/';
            $site_url = 'http://' . strtolower($site->domain);
        } else {
            $reset_link = \Domain::appRoute( $subdomain, '/sign/forgot');
            $login_url  = \Domain::appRoute( $subdomain, "/sign/in/");
            $site_url = \Domain::appRoute($subdomain, '');
        }

		$data = [
			'site_url' => $site_url,
            'subdomain' => $subdomain,
			'site_name' => $site->name,
			'user_email' => $user->email,
			'reset_url' => $reset_link,
			'login_url' => $login_url,
			'user_password' => $password,
			'site_logo' => isset($site_logo) ? $site_logo->value : '',
			'header_bg_color' => $header_bg_color,
			'smartmember_member' => $site->subdomain == 'sm' ? true : false
		];

		if( $cbreceipt )
			$data['cbreceipt'] = $cbreceipt;



		$from = "noreply@" . ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' );

        $email->addTo($user->email)
            ->setFrom($from)
            ->setSubject( $site_welcome_subject )
            ->setHtml( $site_welcome_content );
        \Log::info('Send welcome email for' . $user->email);
        self::sendEmail($email, true, $site);

		\App\Models\Event::Log( 'sent-welcome-email', array(
			'user_id' => $user->id,
			'site_id' => $site && $site->id ? $site->id : 0,
			'email' => $user->email
		) );
    }

    public static function sendForgotPasswordEmail($user, $site)
    {
        //$site = \Domain::getSite();

        if (!$site)
            $site = Site::where('subdomain', 'training')->first();


        $email = new \SendGrid\Email();

		$subdomain = !empty( $site ) && !empty( $site->subdomain ) ? $site->subdomain : 'help';
		$site_name = !empty( $site ) && !empty( $site->name ) ? $site->name : '';
        $header_bg_color = !empty( $site ) ? $site->getHeaderBackgroundColor() : '';
        \Log::info('Reset password');
        if (!empty($site->domain))
        {
            \Log::info('This is the custom domain');
            $reset_link = 'http://' . strtolower($site->domain) .  '/sign/reset?reset_hash=' . $user->reset_token;
        } else {
            $reset_link = \Domain::appRoute (\Domain::getSubdomain(), '/sign/reset?reset_hash=' . $user->reset_token);
        }

        $data = array();
        $data['email'] = $user->email;
        $data["reset_link"] = $reset_link;
        $data['subdomain'] = $subdomain;
        $site_meta = $site->meta_data()->where('key', 'site_logo')->select(['value'])->first();
        $data['site_logo'] = isset($site_meta) ? $site_meta->value : '';
        $data['header_bg_color'] = !empty( $header_bg_color ) ? $header_bg_color : '';

        $view = \View::make("email.user.resetpassword", $data)->render();

		$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

        $email->addTo($user['email'])
              ->setFrom($from)
              ->setSubject("Password Reset for " . ( !empty( $site_name ) ? $site_name . ' - ' : '' ) . (!empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com'))
              ->setHTML($view);

        self::sendEmail($email, true, $site);

		\App\Models\Event::Log( 'sent-forgot-password', array(
			'user_id' => $user->id,
			'site_id' => $site && $site->id ? $site->id : 0,
			'email' => $user->email,
			'extra meta test' => 'whatever'
		) );
    }

    public static function sendPurchaseEmail($transaction, $pass=false, $cbreceipt=false)
    {
        $site_name = "training";
        $site = Site::where('id',$transaction->site_id)->first();

        if ( $site )
        {
            $site_name = $site->name;
            if( empty( $site_name ) )
                $site_name = $site->subdomain;

            $site_domain = $site->subdomain;
            $header_bg_color = $site->getHeaderBackgroundColor();
        }

        $to = $transaction->email;

        $subject = 'Welcome to ' . $site_name . '! Inside are your Login Details';
        if (!empty($site->domain))
        {
            $login_url = 'http://' . strtolower($site->domain) . '/sign/in/';
        } else {
            $login_url  = \Domain::appRoute( $site_domain, "/sign/in/");
        }



        if( !empty( $transaction->user_id ) )
        {
            $user = User::find( $transaction->user_id );
            if (!empty($site->domain))
            {
                $reset_link = 'http://' . strtolower($site->domain) . '/sign/forgot';
            } else {
                $reset_link = \Domain::appRoute( $site_domain, '/sign/forgot');
            }


            $data                    = array();
            $data[ 'site_name' ]     = $site_name;
            $data[ 'user_email' ]    = $user->email;
            $data[ 'reset_url' ]     = $reset_link;

            if( !empty( $transaction['user_password'] ) )
                $data[ 'user_password' ] = $transaction['user_password'];

            $data[ 'login_url' ]     = $login_url;
            $site_meta               = $site->meta_data()->where( 'key', 'site_logo' )->select( [ 'value' ] )->first();
            $data[ 'site_logo' ]     = isset( $site_meta ) ? $site_meta->value : '';
            $data[ 'header_bg_color']= !empty( $header_bg_color ) ? $header_bg_color : '';
            $data[ 'subdomain' ]     = $site->subdomain;

			if( $pass )
				$data['access_level'] = $pass->accessLevel->name;

			if( $cbreceipt )
				$data['cbreceipt'] = $cbreceipt;

            if (!empty($site->domain))
            {
                $cb_link = 'http://' . strtolower($site->domain) . '?cbreceipt=' . $cbreceipt;
            } else {
                $cb_link = \Domain::appRoute( $site_domain, '?cbreceipt=' . $cbreceipt);
            }

            $data['cb_link'] = $cb_link;

            $view = '';

            $smartmember_unique = false;
            if( $smartmember_unique && $site->id == 1 )
            {
                $view = view( 'email.transaction.purchase_smartmember', $data );
            }
            else
            {
                $view = view( 'email.transaction.purchase', $data );
            }

			$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

            //TODO: Add site specific credentials for sending email here.
            $email = new \SendGrid\Email();
            $email->addTo( $to )
                ->setFrom( $from )
                ->setSubject( $subject )
                ->setHtml( $view );

            self::sendEmail( $email, true, $site );

			\App\Models\Event::Log( 'sent-purchase-email', array(
				'user_id' => $user->id,
				'site_id' => $site && $site->id ? $site->id : 0,
				'email' => $user->email,
				'cbreceipt' => $cbreceipt,
				'transaction_id' => $transaction->transaction_id,
				'access-level' => !empty( $data['access_level'] ) ? $data['access_level'] : ''
			) );
        }
    }

    public static function sendAccessPassEmail($passes)
    {
        $pass = $passes[0];
        $site = $pass->site;
        $user = $pass->user;

        $site_name = "training";
        if ( $site )
        {
            $site_name = $site->name;
            if( empty( $site_name ) )
                $site_name = $site->subdomain;

            $site_domain = $site->subdomain;
            $header_bg_color = $site->getHeaderBackgroundColor();
        }

        $to = $user->email;

        $login_url  = \Domain::appRoute( $site_domain, "/sign/in/");

        if( !empty( $user->id ) )
        {
            $reset_link = \Domain::appRoute( $site_domain, '/sign/forgot');

            $data                    = array();
            $data[ 'site_name' ]     = $site_name;
            $data[ 'user_email' ]    = $user->email;
            $data[ 'reset_url' ]     = $reset_link;

            $data[ 'login_url' ]     = $login_url;
            $site_meta               = $site->meta_data()->where( 'key', 'site_logo' )->select( [ 'value' ] )->first();
            $data[ 'site_logo' ]     = isset( $site_meta ) ? $site_meta->value : '';
            $data[ 'header_bg_color' ]     = isset( $header_bg_color ) ? $header_bg_color : '';
            $data[ 'subdomain' ]     = $site->subdomain;

			$access_level_name = '';

			foreach( $passes as $key=>$val ) {
				if( $key > 0 && count( $passes ) > 2 )
					$access_level_name .= ', ';

				if( $key + 1 == count( $passes ) && count( $passes ) > 1 )
					$access_level_name .= ' and ';

				$access_level_name .= $val->accessLevel->name;
			}

            $data['access_level'] = $access_level_name;

			$subject = 'Welcome to ' . $site_name . '! Here is your access to ' . $access_level_name;

            $view = '';

            $smartmember_unique = false;
            if( $smartmember_unique && $site->id == 1 )
                $view = view( 'email.transaction.purchase_smartmember', $data );
            else
                $view = view( 'email.transaction.purchase', $data );

            //TODO: Add site specific credentials for sending email here.
            $email = new \SendGrid\Email();

			$default_subject = self::getWelcomeDefaultSubject();
			$default_email_content = self::getWelcomeDefaultContent();

			$site_welcome_subject = $site->meta_data()->where('key', 'welcome_email_subject')->select(['value'])->first();
			$site_welcome_subject = isset( $site_welcome_subject ) ? $site_welcome_subject->value : $default_subject;

			$site_welcome_subject = str_replace( '%site_name%', $site->name, $site_welcome_subject );

			$site_welcome_content = $site->meta_data()->where('key', 'welcome_email_content')->select(['value'])->first();
			$site_welcome_content = isset( $site_welcome_content ) ? $site_welcome_content->value : $default_email_content;

			$password = '';

			if( !empty( $pass->password ) )
				$password = $pass->password;

			$replacements = [
				'%site_name%' => $site->name,
				'%login_details%' => self::getLoginInfo( $user, $site, $password, $access_level_name ),
				'%site_url%' => $site->domain ? $site->domain . '?signin' : 'http://'.$site->subdomain . '.smartmember.com?signin',
				'%site_subdomain%' => $site->subdomain,
				'%login_button%' => '' //this is to get rid of it for anyone who already implemented it
			];

			$site_welcome_content = str_replace( array_keys( $replacements ), array_values( $replacements ), $site_welcome_content );

			$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

            $email->addTo( $to )
                ->setFrom( $from )
                ->setSubject( $site_welcome_subject )
                ->setHtml( $site_welcome_content );

            self::sendEmail( $email, true, $site );
        }
    }

    public static function sendAccountLinkEmail($account)
    {
     
        $site = Site::where('subdomain', 'training')->first();
        $verify_link = \Domain::appRoute ('my', '/admin/account/settings?verification_hash=' . $account->verification_hash);

        $email = new \SendGrid\Email();

        $subdomain = !empty( $site ) && property_exists( $site, 'subdomain' ) && !empty( $site->subdomain ) ? $site->subdomain : 'training';
        $site_name = !empty( $site ) && property_exists( $site, 'subdomain' ) && !empty( $site->name ) ? $site->name : '';
        $header_bg_color = !empty( $site ) ? $site->getHeaderBackgroundColor() : '';

        $data = array();
        $data['email'] = $account->linked_email;
        $data["verify_link"] = $verify_link;
        $data['subdomain'] = $subdomain;
        $site_meta = $site->meta_data()->where('key', 'site_logo')->select(['value'])->first();
        $data['site_logo'] = isset($site_meta) ? $site_meta->value : '';
        $data['header_bg_color'] = !empty( $header_bg_color ) ? $header_bg_color : '';

        $view = \View::make("email.user.linkaccount", $data)->render();

		$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

        $email->addTo($data['email'])
              ->setFrom( $from )
              ->setSubject("Verify Account Link Request")
              ->setHTML($view);

        self::sendEmail($email, true, $site);
    }

    public static function sendTestEmail($email_address,$subject,$message, $site)
    {
        $email = new \SendGrid\Email();

		//$current_company_id = Company::getOrSetCurrentCompany();

		$emailSetting = EmailSetting::where( 'site_id', $site->id )->first();

        $email->addTo($email_address)
            ->setFrom( !empty( $emailSetting->sending_address ) ? $emailSetting->sending_address : "noreply@smartmember.com")
            ->setSubject($subject)
            ->setHtml( $message );

        self::sendEmail($email, true, $site);
    }

    public static function checkCredentials($username, $password)
    {
        $email = new \SendGrid\Email();

        $email->addTo("noreply@smartmember.com")
            ->setFrom("noreply@smartmember.com")
            ->setSubject('Credentials Test')
            ->setHtml( 'Credentials Test' );

        try {
            $sendgrid = new SendGrid( $username, $password );
            $sendgrid->send( $email );
        } catch (\SendGrid\Exception $e) {
            return false;
        }

        return true;
        
    }

    //TODO sendEmail & processEmail can be refactored and merged!!
    public static function sendEmail($email, $useTrainingKeyAsNeeded = false, $site)
    {
        //$site = \Domain::getSite();


			$site_id = $site->id;


			$emailSetting = AppConfiguration::whereSiteId($site_id)->whereType('sendgrid')->whereDisabled(0)->orderBy('default','desc')->select( [ 'username', 'password' ] )->first();


        /*if( empty( $emailSetting ) && !empty( $site ) )
        {
			$company_id = $site->company_id;

			$emailSetting = AppConfiguration::where( function( $query ) use ($company_id)
			{
				$query->whereCompanyId( $company_id );
				$query->where( function ( $query2 )
				{
					$query2->whereNull( 'site_id' );
					$query2->orwhere( 'site_id', 0 );
				} );
			})->whereType('sendgrid')->whereDisabled(0)->orderBy('default','desc')->select( [ 'username', 'password' ] )->first();
        }*/

        if( empty( $emailSetting ) && empty( $useTrainingKeyAsNeeded ) )
        {
            return;
        }
        elseif( !empty( $emailSetting ) )
        {
            $username = !empty( $emailSetting->username ) ? $emailSetting->username : '';
            $password = !empty( $emailSetting->password ) ? $emailSetting->password : '';
        }

        if( empty( $username ) || empty( $password ) )
        {
            if( $useTrainingKeyAsNeeded )
            {
                $username = \Config::get("integration.sendgrid.api_user");
                $password = \Config::get("integration.sendgrid.api_pass");
            }
            else
            {
                return;
            }

        }

		try {
			$sendgrid = new SendGrid( $username, $password );
			$sendgrid->send( $email );
		} catch (\SendGrid\Exception $e) {
			if( $useTrainingKeyAsNeeded )
			{
				$username = \Config::get("integration.sendgrid.api_user");
				$password = \Config::get("integration.sendgrid.api_pass");

				$sendgrid = new SendGrid( $username, $password );
				$sendgrid->send( $email );
			}
			else
			{
				$error = array( "message" => 'E-mail could not be sent, please check your Sendgrid Credentials.', "code" => 500 );
				return response()->json( $error )->setStatusCode( 404 );
			}
		}
    }

    public static function processEmail($theEmail)
    {
        $site_id = !empty($theEmail->site_id) ? $theEmail->site_id : 0;
        
        $site = \Domain::getSite();

		$emailSetting = EmailSetting::where( 'site_id', $site_id )->first();

		if( $theEmail->sendgrid_integration )
		{
			//checking site ids and company id just to make sure the integration belongs to the same company as the e-mail
			$site_ids = Site::whereSiteId( $site_id )->select('id')->lists('id');
			$sendgrid_settings = AppConfiguration::whereId( $theEmail->sendgrid_integration)->where(function($q) use ($site_id){
				$q->where('site_id',$site_id);
			})->whereType('sendgrid')->whereDisabled(0)->select( [ 'username', 'password' ] )->first();
		}

		if( empty( $sendgrid_settings ) )
		{
			$sendgrid_settings = AppConfiguration::where( function( $query ) use ($site_id)
			{
				$query->whereSiteId( $site_id );
			})->whereType('sendgrid')->whereDisabled(0)->orderBy('default','desc')->select( [ 'username', 'password' ] )->first();
		}

        if ( empty( $emailSetting ) || empty($sendgrid_settings) || !isset($sendgrid_settings->username) || !isset($sendgrid_settings->password) )
            \App::abort(403, "Make sure you have set up E-mail Settings and at least one Sendgrid Integration");

        $email = new \SendGrid\Email();

        $email->addTo($theEmail->admin)
                ->setFromName($emailSetting->full_name)
                ->setFrom($emailSetting->sending_address)
                ->setReplyTo($emailSetting->replyto_address)
                ->setSubject($theEmail->subject)
                ->setHtml($theEmail->content)
                ->setText(strip_tags($theEmail->content));

        $sendgrid = new SendGrid( $sendgrid_settings->username, $sendgrid_settings->password );
        try 
        {
            $sendgrid->send( $email );
        } 
        catch (\SendGrid\Exception $e) 
        {
            \Log::error($e->getMessage());
            \App::abort(403, "Something went wrong when tried to send your email. Check to make sure your SendGrid credentials are correct");
        }

        return $theEmail;
    }


    public static function ApplySubstitutionSymbols( $message )
    {
        $message = str_replace( '@@@', '%', $message );

        return $message;
    }

    public static function ConvertMessageToText( $text )
    {
        //TODO: strip out html
        $text = strip_tags( SendGridEmail::ApplySubstitutionSymbols( $text ) );

        return $text;
    }

    public static function ConvertMessageToHtml( $text )
    {
        $text = SendGridEmail::ApplySubstitutionSymbols( $text );

        return $text;
    }

    public static function processEmailQueue($theEmail)
    {
        $site_id = !empty($theEmail->site_id) ? $theEmail->site_id : 0;

        if ($site_id == 0)
            \App::abort(403, "Failed to process email");

		$emailSetting = EmailSetting::where( 'site_id', $site_id )->first();

		if( $theEmail->sendgrid_integration )
		{
			$sendgrid_settings = AppConfiguration::whereId( $theEmail->sendgrid_integration)->where(function($q) use ($site_id){
				$q->orwhere('site_id',$site_id);
			})->whereType('sendgrid')->whereDisabled(0)->select( [ 'username', 'password' ] )->first();
		}

		if( empty( $sendgrid_settings ) )
		{
			$sendgrid_settings = AppConfiguration::where( function( $query ) use ($site_id)
			{
				$query->whereSiteId( $site_id );
			})->whereType('sendgrid')->whereDisabled(0)->orderBy('default','desc')->select( [ 'username', 'password' ] )->first();
		}

		if ( empty($sendgrid_settings) || !isset($sendgrid_settings->username) || !isset($sendgrid_settings->password) )
			\App::abort(403, "Make sure you have set up E-mail Settings and at least one Sendgrid Integration");

        $html_version_of_message = SendGridEmail::ConvertMessageToHtml($theEmail->content);
        $text_version_of_message = SendGridEmail::ConvertMessageToText($theEmail->content);

        $email = new \SendGrid\Email();

        foreach( $theEmail->to as $key=>$val )
        {
            $email->addSmtpapiTo($val);
        }

		$from_address = !empty( $theEmail->original_email->mail_sending_address ) ? $theEmail->original_email->mail_sending_address : ( !empty( $emailSetting ) ? $emailSetting->sending_address : '' );
		$reply_address = !empty( $theEmail->original_email->mail_reply_address ) ? $theEmail->original_email->mail_reply_address : ( !empty( $emailSetting ) ? $emailSetting->replyto_address : $from_address );
		$from_name = !empty( $theEmail->original_email->mail_name ) ? $theEmail->original_email->mail_name : ( !empty( $emailSetting ) ? $emailSetting->full_name : $from_address );

		if( empty( $from_address ) || empty( $reply_address ) || empty( $from_name ) )
			\App::abort(403, "Make sure you have a from address, reply address, and from name set.");

            $email->setFromName($from_name)
            ->setFrom($from_address)
            ->setReplyTo($reply_address)
            ->setSubject($theEmail->subject)
            ->setHtml($html_version_of_message)
            ->setText(strip_tags($text_version_of_message));

        if ($theEmail->substitutions) {
            $email->setSubstitutions($theEmail->substitutions);
        }

        $sendgrid = new SendGrid($sendgrid_settings->username, $sendgrid_settings->password);
        try {
            $sendgrid->send($email);
        } catch (\SendGrid\Exception $e) {
            \Log::error($e->getMessage());
            \App::abort(403, "Something went wrong when tried to send your email. Check to make sure your SendGrid credentials are correct");
        }

        return $theEmail;
    }

    public static function getDomain( $site ) {
		if( !empty( $_SERVER['HTTP_HOST'] ) )
		{
			$domain = $_SERVER[ 'HTTP_HOST' ];
			$parts  = explode( ".", $domain );
			$tld    = array_pop( $parts );
		}
		else
		{
			$tld = 'com';
		}

        if( !empty( $site ) && !empty( $site->domain ) )
            return $site->domain;
        elseif( !empty( $site ) )
            return $site->subdomain . '.smartmember.' . $tld;
		else
			return 'help.smartmember.' . $tld;
    }

    public static function sendNewSupportEmail($user , $ticket , $site) 
    {
        $email = new \SendGrid\Email();
        if (isset($site))
        {
            $site_logo = $site->meta_data()->where('key', 'site_logo')->select(['value'])->first();
            $header_bg_color = $site->getHeaderBackgroundColor();
        }
        $view = \View::make("email.support.new", [
                'name' =>  $user['name'],
                'ticket_id' => $ticket->id, 
                'ticket_link' => 'http://' . self::getDomain( $site ) .'/support-ticket/'.$ticket->id ,
                'site_logo' => isset($site_logo) ? $site_logo->value : '',
                'subdomain' => $site->subdomain,
                'ticket'=>$ticket,
                'ticket_subject'  => $ticket->subject,
                'ticket_message' => $ticket->message,
                'header_bg_color' => !empty( $header_bg_color ) ? $header_bg_color : ''
            ])->render();

		$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

        $email->addTo($user['email'])
            ->setFrom( $from )
            ->setSubject( ( $site ? '[' . $site->name . '] ' : '' ) . 'TICKET SUBMITTED: ' . $ticket->subject )
            ->setHtml($view);

        self::sendEmail($email, true, $site);
    }

    public static function sendReplySupportEmail($user , $ticket , $site) 
    {
        $email = new \SendGrid\Email();
        if (isset($site))
        {
            $site_logo = $site->meta_data()->where('key', 'site_logo')->select(['value'])->first();
            $header_bg_color = $site->getHeaderBackgroundColor();
        }
        $view = \View::make("email.support.reply", [
                'name' =>  $user['name'],
                'ticket_id' => $ticket->id, 
                'ticket_link' => 'http://' . self::getDomain( $site ) .'/support-ticket/'.$ticket->id ,
                'site_logo' => isset($site_logo) ? $site_logo->value : '',
                'subdomain' => $site->subdomain,
                'ticket'=>$ticket,
                'ticket_subject'  => $ticket->subject,
                'ticket_message' => $ticket->message,
                'header_bg_color' => !empty( $header_bg_color ) ? $header_bg_color : ''
            ])->render();

		$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

        $email->addTo($user['email'])
            ->setFrom( $from )
            ->setSubject( ( $site ? '[' . $site->name . '] ' : '' ) . ' NEW REPLY: ' . $ticket->subject )
            ->setHtml($view);

        self::sendEmail($email, true, $site);
    }

    public static function sendResolvedSupportEmail($ticket)
    {
        $user = array();
        $user['name'] = $ticket->user_name;
        $user['email'] = $ticket->user_email;

        $site = Site::find( $ticket->site_id );

        if( !empty( $ticket->user_email ) )
        {
            $email = new \SendGrid\Email();
            if( isset( $site ) )
            {
                $site_logo = $site->meta_data()->where( 'key', 'site_logo' )->select( [ 'value' ] )->first();
                $header_bg_color = $site->getHeaderBackgroundColor();
            }
            $view = \View::make( "email.support.resolved", [
                'name' => $user[ 'name' ],
                'ticket_id' => $ticket->id,
                'ticket_link' => 'http://' . self::getDomain( $site ) . '/support-ticket/' . $ticket->id,
                'site_logo' => isset( $site_logo ) ? $site_logo->value : '',
                'header_bg_color' => !empty( $header_bg_color ) ? $header_bg_color : '',
                'ticket'=>$ticket,
                'ticket_subject'  => $ticket->subject,
                'ticket_message' => $ticket->message,
                'subdomain' => $site->subdomain,
                'hash' => md5( $user[ 'email' ] . $ticket->id )
            ] )->render();

			$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

            $email->addTo( $user[ 'email' ] )
                ->setFrom( $from )
                ->setSubject( ( $site ? '[' . $site->name . '] ' : '' ) . 'TICKET RESOLVED: ' . $ticket->subject )
                ->setHtml( $view );

            SupportTicketAction::addAction( $ticket->id, 'rating_requested' );

            self::sendEmail( $email, true, $site );
        }
    }

    public static function sendThreeDayPendingSupportEmail($ticket)
    {
        $user = array();

        if( empty( $ticket->user_email ) && !empty( $ticket->user_id ) )
        {
            $ticket_user = User::find( $ticket->user_id );
            $ticket->user_name = $ticket_user->first_name . ' ' . $ticket_user->first_name;
            $ticket->user_email = $ticket_user->email;
        }

        $user['name'] = $ticket->user_name;
        $user['email'] = $ticket->user_email;

        $site = Site::find( $ticket->site_id );

        if( !empty( $ticket->user_email ) )
        {
            $email = new \SendGrid\Email();
            if( isset( $site ) )
            {
                $site_logo = $site->meta_data()->where( 'key', 'site_logo' )->select( [ 'value' ] )->first();
                $header_bg_color = $site->getHeaderBackgroundColor();
            }
            $view = \View::make( "email.support.three_day", [
                'name' => $user[ 'name' ],
                'ticket_id' => $ticket->id,
                'ticket_link' => 'http://' . self::getDomain( $site ) .'/support-ticket/'.$ticket->id,
                'site_logo' => isset( $site_logo ) ? $site_logo->value : '',
                'header_bg_color' => !empty( $header_bg_color ) ? $header_bg_color : '',
                'ticket'=>$ticket,
                'ticket_subject'  => $ticket->subject,
                'ticket_message' => $ticket->message,
                'subdomain' => $site->subdomain,
                'hash' => md5( $user[ 'email' ] . $ticket->id )
            ] )->render();

			$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

            $email->addTo( $user[ 'email' ] )
                ->setFrom( $from )
                ->setSubject( ( $site ? '[' . $site->name . '] ' : '' ) . 'TICKET PENDING: ' . $ticket->subject )
                ->setHtml( $view );

            SupportTicketAction::addAction( $ticket->id, '3_day' );

            self::sendEmail( $email, true, $site );
        }
    }

    public static function sendFiveDayPendingSupportEmail($ticket)
    {
        $user = array();
        if( empty( $ticket->user_email ) && !empty( $ticket->user_id ) )
        {
            $ticket_user = User::find( $ticket->user_id );
            $ticket->user_name = $ticket_user->first_name . ' ' . $ticket_user->first_name;
            $ticket->user_email = $ticket_user->email;
        }
        $user['name'] = $ticket->user_name;
        $user['email'] = $ticket->user_email;

        $site = Site::find( $ticket->site_id );

        if( !empty( $ticket->user_email ) )
        {
            $email = new \SendGrid\Email();
            if( isset( $site ) )
            {
                $site_logo = $site->meta_data()->where( 'key', 'site_logo' )->select( [ 'value' ] )->first();
                $header_bg_color = $site->getHeaderBackgroundColor();
            }
            $view = \View::make( "email.support.five_day", [
                'name' => $user[ 'name' ],
                'ticket_id' => $ticket->id,
                'ticket_link' => 'http://' . self::getDomain( $site ) .'/support-ticket/'.$ticket->id,
                'site_logo' => isset( $site_logo ) ? $site_logo->value : '',
                'header_bg_color' => !empty( $header_bg_color ) ? $header_bg_color : '',
                'ticket'=>$ticket,
                'ticket_subject'  => $ticket->subject,
                'ticket_message' => $ticket->message,
                'subdomain' => $site->subdomain,
                'hash' => md5( $user[ 'email' ] . $ticket->id )
            ] )->render();

			$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

            $email->addTo( $user[ 'email' ] )
                ->setFrom( $from )
                ->setSubject( ( $site ? '[' . $site->name . '] ' : '' ) . 'TICKET CLOSED: ' . $ticket->subject )
                ->setHtml( $view );

            SupportTicketAction::addAction( $ticket->id, '5_day' );

            self::sendEmail( $email, true, $site );
        }
    }

    public static function sendNewAgentEmail($ticket)
    {
        $site = Site::find( $ticket->site_id );

        $agent = User::find( $ticket->agent_id );

        if( $agent && \SMRole::userHasAccess($site->id,'manage_content', $agent->id ) )
        {
            if( $agent )
            {
                $agent_data[ 'name' ]  = $agent->first_name . ' ' . $agent->last_name;
                $agent_data[ 'email' ] = $agent->email;
                $email                 = new \SendGrid\Email();
                if( isset( $site ) )
                {
                    $site_logo = $site->meta_data()->where( 'key', 'site_logo' )->select( [ 'value' ] )->first();
                    $header_bg_color = $site->getHeaderBackgroundColor();
                }
                $view = \View::make( "email.support.agent_new", [
                    'name' => $agent[ 'name' ],
                    'ticket_id' => $ticket->id,
                    'ticket_link' => 'http://my.smartmember.com/admin/team/helpdesk/ticket/' . $ticket->id,
                    'site_logo' => isset( $site_logo ) ? $site_logo->value : '',
                    'header_bg_color' => !empty( $header_bg_color ) ? $header_bg_color : '',
                    'subdomain' => $site ? $site->subdomain : '',
                    'ticket'=>$ticket,
                    'ticket_subject'  => $ticket->subject,
                    'ticket_message' => $ticket->message
                ] )->render();

				$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

                $email->addTo( $agent[ 'email' ] )
                    ->setFrom( $from )
                    ->setSubject( ( $site ? '[' . $site->name . '] ' : '' ) . 'ASSIGNED NEW TICKET: ' . $ticket->subject )
                    ->setHtml( $view );

                self::sendEmail( $email, true, $site );
            }
        }
    }

    public static function sendAllAgentEmail($emails , $ticket , $site) 
    {
        $email = new \SendGrid\Email();
        if (isset($site))
        {
            $site_logo = $site->meta_data()->where('key', 'site_logo')->select(['value'])->first();
            $header_bg_color = $site->getHeaderBackgroundColor();
        }
        foreach( $emails as $key=>$val )
        {
            $email->addSmtpapiTo($val);
        }

        $view = \View::make("email.support.all_agent", [
                'name' => '',
                'ticket_id' => $ticket->id, 
                'ticket_link' => 'http://my.smartmember.com/admin/team/helpdesk/ticket/' . $ticket->id,
                'site_logo' => isset($site_logo) ? $site_logo->value : '',
                'subdomain' => $site->subdomain,
                'header_bg_color' => !empty( $header_bg_color ) ? $header_bg_color : '',
                'ticket_subject'  => $ticket->subject,
                'ticket_message' => $ticket->message,
                'ticket'=>$ticket
            ])->render();

		$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

        $email->setFrom( $from )
        ->setSubject( ( $site ? '[' . $site->name . '] ' : '' ) . 'NEW TICKET: ' . $ticket->subject )
        ->setHtml($view);

        self::sendEmail($email, true, $site);
    }

    public static function sendReplyAgentEmail($user , $ticket , $site) 
    {
        $email = new \SendGrid\Email();
        if (isset($site))
        {
            $site_logo = $site->meta_data()->where('key', 'site_logo')->select(['value'])->first();
            $header_bg_color = $site->getHeaderBackgroundColor();
        }
        $view = \View::make("email.support.agent_reply", [
                'name' =>  $user['name'],
                'ticket_id' => $ticket->id, 
                'ticket_link' => 'http://my.smartmember.com/admin/team/helpdesk/ticket/' . $ticket->id,
                'site_logo' => isset($site_logo) ? $site_logo->value : '',
                'subdomain' => $site->subdomain,
                'ticket'=>$ticket,
                'ticket_subject'  => $ticket->subject,
                'ticket_message' => $ticket->message,
                'header_bg_color' => !empty( $header_bg_color ) ? $header_bg_color : ''
            ])->render();

		$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

        $email->addTo($user['email'])
            ->setFrom( $from )
            ->setSubject( ( $site ? '[' . $site->name . '] ' : '' ) . 'NEW REPLY: ' . $ticket->subject )
            ->setHtml($view);

        self::sendEmail($email, true, $site);
    }

	public static function sendVerificationCodeEmail($user, $verification_code, $site)
	{
		//$site = \Domain::getSite();

		if (!$site)
			$site = Site::where('subdomain', 'help')->first();

		$email = new \SendGrid\Email();

		$subdomain = !empty( $site ) && !empty( $site->subdomain ) ? $site->subdomain : 'help';
		$site_name = !empty( $site ) && !empty( $site->name ) ? $site->name : '';
		$header_bg_color = !empty( $site ) ? $site->getHeaderBackgroundColor() : '';

		$data = array();
		$data['email'] = $user->email;
		$data["verification_code"] = $verification_code;
		$data['subdomain'] = $subdomain;
		$site_meta = $site->meta_data()->where('key', 'site_logo')->select(['value'])->first();
		$data['site_logo'] = isset($site_meta) ? $site_meta->value : '';
		$data['header_bg_color'] = !empty( $header_bg_color ) ? $header_bg_color : '';

		$view = \View::make("email.user.verificationcode", $data)->render();

		$from = "noreply@" . ( !empty( $site ) ? ( !empty( $site->domain ) ? $site->domain : $site->subdomain . '.smartmember.com' ) : 'smartmember.com' );

		$email->addTo($user['email'])
			->setFrom( $from )
			->setSubject("Verification Code for " . ( !empty( $site_name ) ? $site_name . ' - ' : '' ) . $subdomain . ".smartmember.com")
			->setHTML($view);

		self::sendEmail($email, true, $site);

		\App\Models\Event::Log( 'sent-verification-code', array(
			'site_id' => $site && $site->id ? $site->id : 0,
			'user_id' => $user->id,
			'email' => $user->email,
			'verification-code' => $verification_code
		) );
	}
}

?>