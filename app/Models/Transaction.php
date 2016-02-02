<?php

namespace App\Models;

use App\Models\AppConfiguration\SendGridEmail;
use App\Models\User;
use App\Models\LinkedAccount;
use App\Models\Site;
use App\Models\AccessLevel;
use App\Models\Site\Role;

class Transaction extends Root
{
    protected $table = "transactions";

    public static $JVZOO = "jvzoo";
    public static $STRIPE = "stripe";
    public static $PAYPAL = "paypal";
    public static $CLICKBANK = "clickbank";
    public static $WSO = "wso";
    public static $ZAXAA = "zaxaa";
    public static $INFUSION = "infusion";
    public function site()
    {
        return $this->belongsTo('App\Models\Site');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function refunds()
    {
        return $this->belongsTo('App\Models\User', 'email', 'email');   
    }

    public function type()
    {
        return $this->belongsTo('App\Models\TransactionType', 'transaction_type_id');
    }

    public function accessLevel()
    {
        return $this->belongsTo('App\Models\AccessLevel', 'product_id', 'product_id');
    }

	public function applySearchQuery($query , $value)
	{
		$levels = AccessLevel::where('id','like','%' . $value . '%')->orwhere('product_id','like','%' . $value . '%')->orwhere('name','like','%' . $value . '%')->select('id')->lists('id');
		$product_ids = AccessLevel::where('product_id','!=',0)->whereNotNull('product_id')->where(function($q) use ($value) {
				$q->where('id','like','%' . $value . '%')->orwhere('product_id','like','%' . $value . '%')->orwhere('name','like','%' . $value . '%');
			})
			->select('product_id')->lists('product_id');

		$users = User::where('email','like','%' . $value . "%")->orwhere('first_name','like','%' . $value . '%')->orwhere('last_name','like','%' . $value . '%')->select(array('id'))->get();

		$query = $query->where(function($q) use ($levels, $users,$product_ids){
			$q->whereIn('user_id' , $users);
			$q->orwhere(function($q2) use ($levels){
				$q2->whereIn('product_id', $levels );
			});
			$q->orwhere(function($q2) use ($product_ids){
				$q2->whereIn('product_id', $product_ids );
			});
		})
			->orwhere('transaction_id','like','%' . $value . '%')
			->orwhere('source','like','%' . $value . '%')
			->orwhere('product_id','like','%' . $value . '%')
			->orwhere('name','like','%' . $value . '%');

		return $query;
	}

    public static function createTransaction($data, $type = 'stripe')
    {
        $transaction = NULL;

        //if this is a jvzoo transaction, let's turn the data into the standardized transaction data so everything else
        //gets processed the same way
        switch ($type)
        {
            case Transaction::$JVZOO:
                $data = Transaction::parseJVZooData($data);
                break;
            case Transaction::$CLICKBANK:
                $data = Transaction::parseClickBankData($data);
                break;
            case Transaction::$ZAXAA:
                $data = Transaction::parseZaXaaData($data);
                break;
            case Transaction::$WSO:
                $data = Transaction::parseWSOData($data);
                break;
            case Transaction::$INFUSION:
                $data = Transaction::parseInfusionData($data);
                break;
            default:
                $data = $data;
                break;


        }

        $transaction = Transaction::where('site_id',$data['site_id'])->where('type',$data['type'])->where('source',$data['source'])->where('transaction_id',$data['transaction_id'])->first();

        if( $transaction )
            return;

        //if we have a user id, grab the user by that, otherwise try to find the user by their email address
        if( !empty( $data['user_id'] ) && $data['user_id'] != 'none' )
            $user = User::find( $data['user_id'] );
        elseif( !empty( $data['email'] ) )
            $user = User::whereEmail( $data['email'] )->first();

		if(  isset($user) && !empty( $data['email'] ) )
		{
			$primaryAccount = LinkedAccount::where('linked_email', $data['email'] )
											->where('verified', 1)
											->first();

			if ($primaryAccount)
			{
				$user = User::find($primaryAccount->user_id);
			}
		}

        //if we still don't have a user, let's create the user
        if ( !isset($user) )
        {
            if (!empty($data['email']) && (empty($data['user_id']) || $data['user_id'] == 'none'))
            {
                \Log::info('it should create user for clickbank');
                $user = self::createUserForTransaction( $data );
            }
        }
        else
        {
            $site = Site::find( $data['site_id'] );
            $site->addMember( $user, 'member', '', true );
        }

        $data['user_id'] = $user->id;

        //create the transaction
        $transaction_data = $data;
        if( isset( $transaction_data['subscription_id'] ) )
            unset( $transaction_data['subscription_id'] );
        if( isset( $transaction_data['expired_at'] ) )
            unset( $transaction_data['expired_at'] );
        $transaction = Transaction::create($transaction_data);

        if( strcasecmp($data['type'], 'sale') == 0 )
        {
            //if we created a user then we have the password as part of the user to use in the email, let's set it in the
            //data we send to the processPurchase function
            if( !empty( $user->generated_password ) )
                $transaction['user_password'] = $user->generated_password;

            self::processPurchase( $transaction, $data );
        }
        elseif( strcasecmp($data['type'], 'rfnd') == 0 )
        {
            self::refundTransaction($data);
        }

        return $transaction;
    }

    //if processing a transaction for a user who doesn't exist, this function is used to create and return that user
    public static function createUserForTransaction( $data, $send_email = false )
    {
        //get the reset token and random password generated so the user is set to go
        $reset_token = md5( microtime().rand() );
        $password = User::randomPassword();

        //set up all the data we need to create the user
        $user_data = array(
            'first_name' => !empty( $data['name'] ) ? $data['name'] : '',
            'username' => $data['email'],
            'email' => $data['email'],
            'reset_token' => $reset_token,
            'password' => $password,
        );

        //create our user
        $user = User::create( $user_data );
        $user->email = $data['email'];
		$user->reset_token = $reset_token;
        $user->save();
        //make sure the user has a valid access token to use
        $user->refreshToken();
        //we are just gonna go ahead and verify the user here
        $user->verified = 1;

        //we need to grab the site and add this user to the site
        $site = Site::find( $data['site_id'] );

		$cbreceipt = false;

		if(  !empty( $data['source'] ) && $data['source'] == 'jvzoo' )
			$cbreceipt = $data['transaction_id'];

        $site->addMember($user, 'member', $password, !$send_email, $cbreceipt );

        //save the user so all our changes are updated
        $user->save();

        //since this is a new user they need a company, so here we set up the data and create the company
        // $hash = md5 ( microtime() . rand(0,1000) );
        // $company_data = array(
        //     'name' => $user->first_name."'s Team",
        //     'user_id' => $user->id,
        //     'hash' => $hash
        // );
        // Company::create( $company_data );

        //set the password on the user object so we can send it to the user in the purchase email
        $user->generated_password = $password;

        return $user;
    }

    //handles everything that needs to happen for a sale transaction after the transaction is created
    public static function processPurchase( $transaction, $data )
    {
        //got to increase the site's total revenue so that we don't have to calculate it all the time!
        self::incrementTotalRevenue( $transaction );

        //grab the access level so we have the appropriate id
        if( $data['source'] == 'jvzoo' )
            $access_level = AccessLevel::whereProductId( $data['product_id'] )->whereSiteId($data['site_id'])->first();
        else if ($data['source'] == 'wso')
            $access_level = AccessLevel::whereWsoProductId( $data['wso_product_id'] )->whereSiteId($data['site_id'])->first();
        else if ($data['source'] == 'clickbank')
            $access_level = AccessLevel::whereCbProductId( $data['cb_product_id'] )->whereSiteId($data['site_id'])->first();
        else if ($data['source'] == 'zaxaa')
            $access_level = AccessLevel::whereZaxaaProductId( $data['zaxaa_product_id'] )->whereSiteId($data['site_id'])->first();
        else
            $access_level = AccessLevel::find( $data['product_id'] );

        if( !empty( $transaction->user_id ) )
        {
            //first let's see if the pass already exists
            $access_pass = Role::where('access_level_id', $access_level->id)->where('user_id',$transaction->user_id)->where('site_id',$transaction->site_id)->first();

            //if the access pass didn't exist, create the access pass for our user and send the purchase e-mail
            if( !$access_pass )
            {
                $access_pass = Role::create( array( 'access_level_id' => $access_level->id, 'user_id' => $transaction->user_id, 'site_id' => $transaction->site_id ) );
            }

			$cbreceipt = false;

			if( $transaction->source == 'jvzoo' )
				$cbreceipt = $transaction->transaction_id;

			SendGridEmail::sendPurchaseEmail($transaction, $access_pass, $cbreceipt);

            //use the updatePass function to set the initial expiration date in case this was a subscription, if its not it won't do anything to it
            Role::updatePass( $access_pass, ( !empty( $data['expired_at'] ) ? $data['expired_at'] : false ) );
        }
    }

    public static function incrementTotalRevenue( $transaction )
    {
        $site = Site::find($transaction->site_id);
        $site->total_revenue = $site->total_revenue + $transaction->price;
        $site->save();
    }

    public static function refundTransaction($data)
    {

        if ( ! $data['transaction_id'])
            return;

        $refundTransaction = Transaction::where('transaction_id', $data['transaction_id'])
                                        ->where('type', '!=', 'rfnd');

		if( !empty( $data['site_id'] ) )
			$refundTransaction = $refundTransaction->whereSiteId( $data['site_id'] );

		$refundTransaction = $refundTransaction->first();

        if( !$refundTransaction || !$refundTransaction->user_id )
            return;

		if( $data['source'] == 'jvzoo' )
		{
			$access_level = AccessLevel::where('product_id', $data['product_id']);

			if( !empty( $data['site_id'] ) )
				$access_level = $access_level->whereSiteId( $data['site_id'] );

			$access_level = $access_level->first();
		}
		else
		{
			$access_level = AccessLevel::find($data['product_id'] );
		}

        if ( !$access_level )
            return;

        $access_pass = Role::where('access_level_id', $access_level->id)->where('user_id', $refundTransaction->user_id)->get();

        if( !$access_pass )
            return;

        foreach( $access_pass as $pass )
        {
            $pass->delete();
        }

    }

    private static function parseClickBankData($data)
    {
        $association_hash = md5(microtime().rand());
        $site = Site::whereSubdomain($data['subdomain'])->first();
        $clickbank_secret_key = 'IMBINC';//AppConfiguration::whereSiteId($site->id)->whereType('clickbank')->select('remote_id')->first();

        $message = json_decode(file_get_contents('php://input'));
        $encrypted = $message->{'notification'};
        $iv = $message->{'iv'};
        \Log::info("IV: $iv");
        $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128,
            substr(sha1($clickbank_secret_key), 0, 32),
            base64_decode($encrypted),
            MCRYPT_MODE_CBC,
            base64_decode($iv)), "\0..\32");
        \Log::info("Decrypted: $decrypted");
        $order = json_decode($decrypted);
        $order = (array) $order;
        $fields = array();
        $fields['type']             = strtolower(isset($order['transactionType']) ? $order['transactionType'] : '');
        $fields['source']           = 'clickbank';
        $fields['user_id']          = '';
        $fields['affiliate_id']     = isset($order['affiliate']) ? $order['affiliate'] : '';
        $fields['cb_product_id']       = isset($order['lineItems'][0]->itemNo) ? $order['lineItems'][0]->itemNo : '';
        $fields['transaction_id']   = isset($order['receipt']) ? $order['receipt'] : '';
        $fields['name']             = isset($order['customer']->billing->fullName) ? $order['customer']->billing->fullName : '';
        $fields['email']            = isset($order['customer']->billing->email) ? $order['customer']->billing->email : '';

        $fields['payment_method']   = strtolower(isset($order['paymentMethod']) ? $order['paymentMethod'] : '');
        $fields['price']            = isset($order['totalOrderAmount']) ? $order['totalOrderAmount'] : '';
        $fields['association_hash'] = $association_hash;
        $fields['data']             = json_encode( $data );
        $fields['site_id']          = $site->id;

        $subdomain = \Domain::getSubdomain();

        if ( $subdomain )
        {
            $site = Site::where('subdomain', $subdomain)->first();
            if ($site)
            {
                $fields['site_id'] = $site->id;
            }
        }

        return $fields;
    }

    private static function parseWSOData($data)
    {
        $association_hash = md5(microtime().rand());
        $site = Site::whereSubdomain($data['subdomain'])->first();

        $fields = array();
        $fields['type']             = $data['payment_gross'] > 0 ? "sale" : "rfnd";
        $fields['source']           = 'wso';
        $fields['user_id']          = '';
        $item_number_parts = explode("_", $data['item_number']);
        $item_number = $item_number_parts[0] . "_" . $item_number_parts[1];

        $fields['wso_product_id']   = $item_number;
        $fields['transaction_id']   = $data['payment_gross'] > 0 ? $data['txn_id'] : $data['parent_txn_id'];
        $fields['name']             = isset($data['first_name']) ? $data['first_name'] . ' ' . $data['last_name'] : '';
        $fields['email']            = isset($data['payer_email']) ? $data['payer_email'] : '';
        $fields['payment_method']   = 'paypal';
        $fields['price']            = isset($data['payment_gross']) ? $data['payment_gross'] : '';
        $fields['association_hash'] = $association_hash;
        $fields['data']             = json_encode( $data );
        $fields['site_id']          = $site->id;

        $subdomain = \Domain::getSubdomain();

        if ( $subdomain )
        {
            $site = Site::where('subdomain', $subdomain)->first();
            if ($site)
            {
                $fields['site_id'] = $site->id;
            }
        }

        return $fields;

    }

    private static function parseZaXaaData($data)
    {
        $association_hash = md5(microtime().rand());
        $site = Site::whereSubdomain($data['subdomain'])->first();


        $fields = array();
        $fields['type']             = strtolower(isset($data['trans_type']) ? $data['trans_type'] : '');
        $fields['source']           = 'zaxaa';
        $fields['user_id']          = '';
        //$fields['affiliate_id']     = isset($data['ctransaffiliate']) ? $data['ctransaffiliate'] : '';

        $fields['zaxaa_product_id']       = isset($data['products'][0]['prod_number']) ? $data['products'][0]['prod_number'] : '';
        $fields['transaction_id']   = isset($data['trans_receipt']) ? $data['trans_receipt'] : '';
        $fields['name']             = isset($data['cust_firstname']) ? $data['cust_firstname'] . ' ' . $data['cust_lastname'] : '';
        $fields['email']            = isset($data['cust_email']) ? $data['cust_email'] : '';
        $fields['payment_method']   = strtolower(isset($data['trans_gateway']) ? $data['trans_gateway'] : '');
        $fields['price']            = isset($data['trans_amount']) ? $data['trans_amount'] : '';
        $fields['association_hash'] = $association_hash;
        $fields['data']             = json_encode( $data );
        $fields['site_id']          = $site->id;

        $subdomain = \Domain::getSubdomain();

        if ( $subdomain )
        {
            $site = Site::where('subdomain', $subdomain)->first();
            if ($site)
            {
                $fields['site_id'] = $site->id;
            }
        }

        return $fields;
    }

    private static function parseInfusionData($data)
    {
        $association_hash = md5(microtime().rand());
        $site = Site::whereSubdomain($data['subdomain'])->first();


        $fields = array();
        $fields['type']             = 'sale';
        $fields['source']           = 'infusion';
        $fields['user_id']          = '';
        $fields['affiliate_id']     = '';
        $fields['product_id']       = isset($data['access_level']) ? $data['access_level'] : '';
        $fields['transaction_id']   = isset($data['Id']) ? $data['Id'] . $association_hash : ''; //make transaction unique
        $fields['name']             = isset($data['FirstName']) ? $data['FirstName'] . ' ' . $data['LastName'] : '';
        $fields['email']            = isset($data['Email']) ? $data['Email'] : '';
        $fields['association_hash'] = $association_hash;
        $fields['data']             = json_encode( $data );
        $fields['site_id']          = $site->id;

        $subdomain = \Domain::getSubdomain();

        if ( $subdomain )
        {
            $site = Site::where('subdomain', $subdomain)->first();
            if ($site)
            {
                $fields['site_id'] = $site->id;
            }
        }

        return $fields;
    }

    private static function parseJVZooData($data)
    {
        $association_hash = md5(microtime().rand());
        $site = Site::whereSubdomain($data['subdomain'])->first();


        $fields = array();
        $fields['type']             = strtolower(isset($data['ctransaction']) ? $data['ctransaction'] : '');
        $fields['source']           = 'jvzoo';
        $fields['user_id']          = '';
        $fields['affiliate_id']     = isset($data['ctransaffiliate']) ? $data['ctransaffiliate'] : '';
        $fields['product_id']       = isset($data['cproditem']) ? $data['cproditem'] : '';
        $fields['transaction_id']   = isset($data['ctransreceipt']) ? $data['ctransreceipt'] : '';
        $fields['name']             = isset($data['ccustname']) ? $data['ccustname'] : '';
        $fields['email']            = isset($data['ccustemail']) ? $data['ccustemail'] : '';
        $fields['payment_method']   = strtolower(isset($data['ctranspaymentmethod']) ? $data['ctranspaymentmethod'] : '');
        $fields['price']            = isset($data['ctransamount']) ? $data['ctransamount'] : '';
        $fields['association_hash'] = $association_hash;
        $fields['data']             = json_encode( $data );
        $fields['site_id']          = $site->id;

        $subdomain = \Domain::getSubdomain();

        if ( $subdomain )
        {
            $site = Site::where('subdomain', $subdomain)->first();
            if ($site)
            {
                $fields['site_id'] = $site->id;
            }
        }

        return $fields;
    }

    private static function storeJVZooTransaction($data)
    {
        $association_hash = md5(microtime().rand());
        $site = Site::whereSubdomain($data['subdomain'])->first();

        $fields = array();
        $fields['type']             = strtolower(isset($data['ctransaction']) ? $data['ctransaction'] : '');
        $fields['source']           = 'jvzoo';
        $fields['user_id']          = '';
        $fields['affiliate_id']     = isset($data['ctransaffiliate']) ? $data['ctransaffiliate'] : '';
        $fields['product_id']       = isset($data['cproditem']) ? $data['cproditem'] : '';
        $fields['transaction_id']   = isset($data['ctransreceipt']) ? $data['ctransreceipt'] : '';
        $fields['name']             = isset($data['ccustname']) ? $data['ccustname'] : '';
        $fields['email']            = isset($data['ccustemail']) ? $data['ccustemail'] : '';
        $fields['payment_method']   = strtolower(isset($data['ctranspaymentmethod']) ? $data['ctranspaymentmethod'] : '');
        $fields['price']            = isset($data['ctransamount']) ? $data['ctransamount'] : '';
        $fields['association_hash'] = $association_hash;
        $fields['data']             = json_encode( $data );
        $fields['site_id']          = $site->id;

        $subdomain = \Domain::getSubdomain();

        if ( $subdomain )
        {
            $site = Site::where('subdomain', $subdomain)->first();
            if ($site)
            {
                $fields['site_id'] = $site->id;
            }
        }
        
        $transaction = Transaction::create($fields);

        $action = isset($data['ctransaction']) ? $data['ctransaction'] : '';

        if (strcasecmp($action, 'sale') == 0)
        {
            // Send purchase email
            //SendGridEmail::sendPurchaseEmail($transaction);

        }
        else if (strcasecmp($action, 'rfnd') == 0)
        {
            Transaction::refundTransaction($fields);
        }

        return $transaction;
    }

    static public function resendPurchaseEmail($transaction_id)
    {
        $transaction = Transaction::find( $transaction_id );

        SendGridEmail::sendPurchaseEmail($transaction);
    }
}
