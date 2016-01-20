<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Transaction;
use App\Models\AppConfiguration\Stripe;
use App\Models\AccessLevel;
use App\Models\AccessLevel\Pass;
use App\Models\Site\Role;
use App\Models\User;
use App\Models\AppConfiguration\SendGridEmail;

class TransactionController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new Transaction();
        $this->middleware('auth',['except'=>'store']);
    }

    public function index(){
        $transactions = parent::paginateIndex();
        $transactions['items'] = array_where($transactions['items'], function($key, $value)
        {
            return $value->site_id == \Input::get('site_id');
        });
        $transactions['total_count'] = count($transactions['items']);
        foreach ($transactions['items'] as $transaction) {
            $transaction->user = User::find($transaction->user_id);
			$transaction->access_level = AccessLevel::whereId($transaction->product_id)->orwhere('product_id',$transaction->product_id)->first();
        }
        return $transactions;
    }

    public function summary(){

        $summary["total_refunds"] = $this->model->getOne("select SUM(price) as total_refunds from transactions where site_id = " . $this->site->id . " and type = 'rfnd'" , 'total_refunds');
        $summary["total_sales"] = $this->model->getOne("select SUM(price) as total_sales from transactions where site_id = " . $this->site->id . " and type != 'rfnd'" , 'total_sales');
        $summary["total_today_sales"] = $this->model->getOne("select SUM(price) as total_today_sales from transactions WHERE (created_at BETWEEN CONCAT(CURDATE(),' 05:01:00') AND CONCAT( DATE(DATE_ADD(NOW(), INTERVAL 1 DAY)) ,  ' 4:59:00' ) )  and site_id = " . $this->site->id . " and type != 'rfnd'" , 'total_today_sales');
        $summary["total_yesterday_sales"] = $this->model->getOne("select SUM(price) as total_yesterday_sales from transactions WHERE (created_at BETWEEN CONCAT( DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) ,  ' 05:01:00' )  AND CONCAT( CURDATE() ,  ' 4:59:00' ) ) and site_id = " . $this->site->id . " and type != 'rfnd'" , 'total_yesterday_sales');

        $summary["average_refund"] = $this->model->getOne("select AVG(price) as average_refund from transactions where site_id = " . $this->site->id . " and type = 'rfnd'" , 'average_refund');
        $summary["average_sale"] = $this->model->getOne("select AVG(price) as average_sale from transactions where site_id = " . $this->site->id . " and type != 'rfnd'" , 'average_sale');
        $summary["refund_frequency"] = $this->model->getOne("select COUNT(price) as refund_frequency from transactions where site_id = " . $this->site->id . " and type != 'rfnd' group by DATE(created_at)" , 'refund_frequency');
        $summary["sale_frequency"] = $this->model->getOne("select COUNT(price) as sale_frequency from transactions where site_id = " . $this->site->id . " and type = 'rfnd' group by DATE(created_at)" , 'sale_frequency');

        $summary["number_of_today_sales"] = $this->model->getOne("select count(*) as number_of_today_sales from transactions WHERE (created_at BETWEEN CONCAT(CURDATE(),' 05:01:00') AND CONCAT( DATE(DATE_ADD(NOW(), INTERVAL 1 DAY)) ,  ' 4:59:00' ) ) and site_id = " . $this->site->id . " and type != 'rfnd'" , 'number_of_today_sales');
        $summary["number_of_yesterday_sales"] = $this->model->getOne("select count(*) as number_of_yesterday_sales from transactions WHERE (created_at BETWEEN CONCAT( DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) ,  ' 05:01:00' )  AND CONCAT( CURDATE() ,  ' 4:59:00' ) ) and site_id = " . $this->site->id . " and type != 'rfnd'" , 'number_of_yesterday_sales');
        $summary["number_of_sales"] = Transaction::whereSiteId($this->site->id)->where('type' , '!=' , 'rfnd')->count();
        $summary["number_of_refunds"] = Transaction::whereSiteId($this->site->id)->where('type' , '=' , 'rfnd')->count();
        $summary['last_transaction'] = Transaction::whereSiteId($this->site->id)->orderBy('created_at' , 'DESC')->first(['created_at as last_transaction']);
        if($summary['last_transaction'])
            $summary['last_transaction'] =  $summary['last_transaction']->last_transaction;

        $summary['sales_data'] = Transaction::where('site_id', $this->site->id)
                ->where('type', '!=', 'rfnd')
                ->groupBy(\DB::raw("day"))
                ->select(\DB::raw('SUM(price) as sales, DAY(created_at) day, MONTH(created_at) month, YEAR(created_at) year'))
                ->orderBy('created_at')
                ->get();

         // $summary['refunds_data'] = Transaction::where('site_id', $this->site->id)
         //        ->where('type', '=', 'rfnd')
         //        ->groupBy(\DB::raw("day"))
         //        ->select(\DB::raw('SUM(price) as sales, DAY(created_at) day, MONTH(created_at) month, YEAR(created_at) year'))
         //        ->orderBy('created_at')
         //        ->get();

        return $summary;
    }

    //todo refactor this function down to smaller functions so its more readable
    public function store()
    {
        $request = \Input::all();

        if (!empty($request['subdomain']) && !empty($request['cproditem']) && $request['subdomain'] == 'chrisrecord' && $request['cproditem'] == '191687')
            SendGridEmail::sendTestEmail('todd.nestor@gmail.com', 'chris affiliate data 191687', '<pre>' . print_r($request, true)) . '</pre>';

        if ($request['type'] == Transaction::$STRIPE) {
            if (\App\Helpers\SMAuthenticate::set()) {
                $pass = Role::whereUserId(\Auth::user()->id)->whereSiteId($this->site->id)
                    ->whereAccessLevelId($request['access_id'])->first();

                //$pass = Pass::whereUserId(\Auth::user()->id)->whereSiteId($this->site->id)
                    //->whereAccessLevelId($request['access_id'])->first();

                if ($pass)
                    \App::abort('403', 'You already have an access pass');
            }

            $access_level = AccessLevel::find($request['access_id']);

            $site_meta = new \App\Http\Controllers\Api\SiteMetaDataController;
            $default_currency = $site_meta->getItem('currency');

            if (empty($default_currency))
                $default_currency = 'USD';

            $stripe_data = array(
                'amount' => $access_level->price * 100,
                'token' => $request['token'],
                'currency' => !empty($access_level->currency) ? $access_level->currency : $default_currency,
                'site_id' => $access_level->site_id,
                'plan_id' => $access_level->stripe_plan_id,
                'product_id' => $access_level->id,
                'email' => $request['email'],
                'stripe_integration' => $access_level->stripe_integration
            );

            $subscription = false;
            if ($access_level->payment_interval != 'one_time' && !empty($access_level->stripe_plan_id)) {
                $transaction = Stripe::processSubscription($stripe_data);
                $subscription = true;
            } else
                $transaction = Stripe::processPayment($stripe_data);

            if ($subscription) {
                $successful_subscription = false;
                foreach ($transaction->subscriptions->data as $key => $val) {
                    if ($val['plan']['id'] == $stripe_data['plan_id']) {
                        $successful_subscription = true;
                        $subscription_data = $val;
                    }
                }
            }

            $association_hash = md5(microtime() . rand());
            $transaction_data = array(
                'site_id' => $access_level->site_id,
                'user_id' => \Auth::user() ? \Auth::user()->id : 'none',
                'email' => $request['email'],
                'payment_method' => 'stripe',
                'type' => 'sale',
                'source' => 'stripe',
                'data' => json_encode($transaction),
                'association_hash' => $association_hash,
                'product_id' => $access_level->id,
                'transaction_id' => $transaction->id,
            );

            if (!empty($successful_subscription)) {
                $transaction_data['price'] = $subscription_data['plan']['amount'] / 100;
                $transaction_data['subscription_id'] = $subscription_data['id'];
                $transaction_data['expired_at'] = $subscription_data['current_period_end'];
            } elseif ($transaction->paid) {
                $transaction_data['price'] = $access_level->price;
            } else {
                \App::abort('400', 'Transaction could not be completed');
            }

            return Transaction::createTransaction($transaction_data);
        } else if ($request['type'] == 'jvzoo') {
            return Transaction::createTransaction($request, Transaction::$JVZOO);
        } else if ($request['type'] == 'clickbank') {
            \Log::info('Got a clickbank transaction');
            return Transaction::createTransaction($request, Transaction::$CLICKBANK);
        } else if ($request['type'] == 'zaxaa') {
            \Log::info('Got a zaxaa transaction');
            return Transaction::createTransaction($request, Transaction::$ZAXAA);
        } else if ($request['type'] == 'infusion') {
            return Transaction::createTransaction($request, Transaction::$INFUSION);
        } else if ($request['type'] == 'wso')
        {
            return Transaction::createTransaction($request, Transaction::$WSO);
        }

    	return $request;
    }

    public function resendPurchaseEmail( $id ) {
        Transaction::resendPurchaseEmail( $id );

        return;
    }
}