<?php

namespace App\Http\Controllers\AppConfiguration;

use App\Http\Controllers\AppConfigurationController;
use App\Models\AppConfiguration\Paypal;


use App\Models\Site;
use App\Models\AccessLevel\Pass;
use App\Models\Transaction;
use App\Models\AccessLevel;
use App\Models\AppConfiguration;
use App\Models\SiteMetaData;
use Input;
use Curl;
use App;
use Config;
use Socialize;


class PaypalController extends AppConfigurationController
{
    public function __construct()
    {
        $this->model = new Paypal();
        parent::__construct();
		$this->middleware('auth',['except'=>array('anyProcess','anyPayment')]);
    }

    public function getAuth($id='')
    {
        $response = parent::getAuth($id);
    }

    public function getAccessToken()
    {
        $response = [];
        if (Input::has('code')) {
            $response = $this->model->getAccessToken(Input::get('code'));
        }
        if (!isset($response["access_token"])) {
            \App::abort(401, "Something went wrong, please try again later");
        }

        $response["site_id"] = $this->site->id;
        $data = $this->model->integrate($response);
        header('Location: ' . \Domain::appRoute(Input::get('state'), "/admin/integrations/configured"));
        exit;
    }


    public function getIntegration()
    {
        $app_configuration_instance = AppConfiguration::whereType('paypal')->whereSiteId($this->site->id)->first();
        if ($app_configuration_instance)
        {
            $currency = SiteMetaData::whereKey('currency')->whereSiteId($this->site->id)->first();
            if (!$currency) {
                $app_configuration_instance->currency = "USD";
            } else {
                $app_configuration_instance->currency = $currency->value;
            }
        }
        return $app_configuration_instance;
    }

    public function postStore()
    {
        if (Input::has('currency'))
        {
            $currency = SiteMetaData::whereKey('currency')->whereSiteId($this->site->id)->first();
            if ($currency)
                $currency->update(['value' => Input::get('currency')]);
            else
                SiteMetaData::insert(['site_id' => $this->site->id, 'key' => 'currency', 'value' => Input::get('currency')]);
        }

        $app_configuration_instance = AppConfiguration::whereType('paypal')->whereSiteId($this->site->id)->first();
        if ($app_configuration_instance) {
            $app_configuration_instance->remote_id = Input::get('remote_id');
            $app_configuration_instance->save();
            return $app_configuration_instance;
        }

        return AppConfiguration::create(array(
            "remote_id" => Input::get('remote_id'),
            "type" => "paypal",
            "site_id" => $this->site->id
        ));
    }

    public function anyProcess($id, $user_id)
    {
        if (!\Input::get('payment_status') || (\Input::get('payment_status') != 'Completed' && \Input::get('payment_status') != 'Refunded'))
            return;

        $access_level = AccessLevel::find($id);
        $association_hash = md5(microtime() . rand());

        $data = array(
            "site_id" => $access_level->site_id,
            "user_id" => $user_id,
            "product_id" => $access_level->id,
            "transaction_id" => \Input::get('parent_txn_id') ? \Input::get('parent_txn_id') : \Input::get('txn_id'),
            "source" => "paypal",
            "type" => \Input::get('payment_gross') > 0 ? "sale" : "rfnd",
            "name" => \Input::get('first_name') . " " . \Input::get('last_name'),
            "email" => \Input::get('payer_email'),
            "price" => \Input::get('payment_gross'),
            "payment_method" => 'paypal',
            "data" => json_encode(\Input::all()),
            'association_hash' => $association_hash,
        );

        if (\Input::get('txn_type') == 'subscr_payment')
            $data['subscription_id'] = \Input::get('subscr_id');

        $transaction = Transaction::createTransaction($data, "paypal");

        //below was for testing to make sure the access pass got created
        //\App\Models\Integration\SendGridEmail::sendTestEmail('todd.nestor@gmail.com','paypal ipn access pass','<br><pre>'.print_r( $pass, true ).'</pre>');
        return 1;
    }

    public function getSuccess()
    {
        return $this->model->processPayment();
    }

    public function anyPayment()
    {
        $access_level = AccessLevel::find(Input::get('level'));
        $currency_settings = SiteMetaData::whereKey('currency')->whereSiteId($access_level->site_id);
        if (!isset($currency_settings))
            $currency_settings  = 'USD';
        $app_configuration_instance = AppConfiguration::whereSiteId($access_level->site_id)->whereType('paypal')->first();
        if (!$app_configuration_instance)
            \App::abort(403, "No integration found");

        if ($access_level)
            return 'https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=' . $app_configuration_instance->remote_id . '&item_name=' . $access_level->name . '&amount=' . $access_level->price . '%2e95&currency_code=' . $currency_settings . '&return=' . $access_level->redirect_url . '&cancel_return=' . $access_level->information_url . '&notify_url=http://api.smartmember.in/paypal/process';
        \App::abort(404, "No such access level exists");
    }

}
