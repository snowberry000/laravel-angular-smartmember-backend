<?php namespace App\Http\Controllers\AppConfiguration;

use App\Http\Controllers\AppConfigurationController;

use App\Models\AppConfiguration\SendGridEmail;
use App\Models\EmailSetting;
use App\Models\Email;
use App\Models\UserOptions;
use SendGrid;
use App\Models\Transaction;
use Auth;

class SendGridController extends AppConfigurationController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new SendGridEmail();
        $this->middleware('auth'); 
    }

    public function postSettings()
    {
        $settings = EmailSetting::where('site_id', $this->site->id)->first();
        if ($settings)
        {
            $settings->fill(\Input::all());
            $settings->save();
        } 
        else 
        {
            // First time registering email settings.
            $params = array_merge(\Input::all(), array( 'site_id' => $this->site->id ) );
            $settings = EmailSetting::create($params);
        }

        return $settings;
    }

    public function getSettings() {
		if( !empty( $this->site ) )
		{
			return EmailSetting::where( 'site_id', $this->site->id )->first();
		}
		else
			\App::abort('408','You must be signed into a team to access email settings.');
    }

    public function sendPurchaseEmail()
    {
        $transaction_id = \Input::get('transaction_id');
        $transaction = Transaction::whereTransactionId($transaction_id)->first();
        if ($transaction)
            $this->model->sendPurchaseEmail($transaction, false, $transaction_id);
    }

    public function postPreview() {
        $email = new Email;
        $email->site_id = \Input::has('site_id') ? \Input::get('site_id') : '';
        $email->subject = \Input::has('subject') ? \Input::get('subject') : '';
        $email->content = \Input::has('content') ? \Input::get('content') : '';
        $email->admin = \Input::has('admin') ? \Input::get('admin') : '';

        return SendGridEmail::processEmail($email);
    }
}